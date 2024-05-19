<?php

namespace TechStudio\Core\app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Core\app\Models\UserProfile;
use TechStudio\Core\app\Services\Search\SearchService;
use TechStudio\Blog\app\Http\Resources\ArticleResource;
use TechStudio\Core\app\Http\Resources\ArticlesResource;

class SearchController extends Controller
{
    public function searchUser(Request $request)
    {
        $txt = $request->query->get('query');
        $res = [];
        if ($txt) {
            $users = UserProfile::where('status', 'active')->where(function ($q) use ($txt) {
                $q->Where('first_name', 'like', '%' . $txt . '%')
                    ->orWhere('last_name', 'like', '%' . $txt . '%')
                    ->orWhere('registration_phone_number', 'like', '%' . $txt . '%');
            })->take(10)->get(['first_name', 'last_name', 'user_id', 'avatar_url']);

            $res = $users->map(fn ($user) => [
                'id' => $user->user_id,
                'displayName' => $user->getDisplayName(),
                'avatarUrl' => $user->avatar_url,
            ]);
        }

        return response()->json($res);
    }

    public function search(Request $request)
    {
        if ($request->filled('query')) {
            $keyword = $request->get('query');
            $result = Article::with('category', 'author')
                ->where('title', 'like', '%' . $keyword . '%')
                ->orWhere('content', 'like', '%' . $keyword . '%')
                ->take(5)->get()->map(function ($item) {
                    $item['type'] = 'article';
                    return $item;
                });
            return response()->json(ArticleResource::collection($result));
        }
    }

    /**
     * @LRDparam type string|in:article,user,tag,category
     * // either space or pipe
     * @LRDparam keyword string
     */
    public function searchData()
    {
        $data = SearchService::search(request()->type, request()->keyword);
        return $data;
    }

    /**
     * @LRDparam keyword string|required|max:32
     * // either space or pipe
     * @LRDparam type Enum|required|blog,course
     */
    public function generalSearch()
    {
        $keyword = '%' . request()->keyword . '%';
        $type = request()->type;

        $blogModule = 'TechStudio\Blog\app\Models';
        $lmsModule = 'TechStudio\Blog\app\Models\Article';
        $commiunityModule = 'TechStudio\Blog\app\Models\Article';

        if ($type == 'blog' && class_exists($blogModule . '\Article')) {
            $data = Article::where('title', 'like', $keyword)
                ->orWhere('summary', 'like', $keyword)
                ->orWhere('content', 'like', $keyword)
                ->where('status', 'published')->get();
            return new ArticlesResource($data);
        }
    }
}
