<?php

namespace TechStudio\Core\app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Main\StartupResource;
use App\Http\Resources\Main\StartupsResource;
use App\Http\Resources\Product\ProductsResource;
use App\Models\Product;
use App\Models\Startup;
use TechStudio\Lms\app\Models\Course;
use TechStudio\Blog\app\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use TechStudio\Core\app\Models\UserProfile;
use TechStudio\Community\app\Models\ChatRoom;
use TechStudio\Community\app\Models\Question;
use TechStudio\Core\app\Services\Search\SearchService;
use TechStudio\Lms\app\Http\Resources\CoursesResource;
use TechStudio\Blog\app\Http\Resources\ArticleResource;
use TechStudio\Core\app\Http\Resources\ArticlesResource;
use TechStudio\Community\app\Http\Resources\ChatRoomsResource;
use TechStudio\Community\app\Http\Resources\QuestionsOldResource;

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
     * @LRDparam type Enum|required|blogs,courses,rooms,questions
     */
    public function generalSearch()
    {
        $keyword = '%' . request()->keyword . '%';
        $type = request()->type;

        $blogModule = 'TechStudio\Blog\app\Models';
        $lmsModule = 'TechStudio\Lms\app\Models';
        $commiunityModule = 'TechStudio\Community\app\Models';

        if ($type == 'blogs' && class_exists($blogModule . '\Article')) {
            $data = Article::withoutGlobalScopes()
                ->where('status', 'published')
                ->where(function (Builder $query) use ($keyword) {
                    $query->where('title', 'like', $keyword)
                        ->orWhere('content', 'like', $keyword);
                })->latest()->paginate();
            return new ArticlesResource($data);
        }

        if ($type == 'courses' && class_exists($lmsModule . '\Course')) {
            $data = Course::withoutGlobalScopes()
                ->where('status', 'published')
                ->where(function (Builder $query) use ($keyword) {
                    $query->where('title', 'like', $keyword)
                        ->orWhere('description', 'like', $keyword)
                        ->orWhere('faq', 'like', $keyword);
                })->latest()->paginate();
            return new CoursesResource($data);
        }

        if ($type == 'rooms' && class_exists($commiunityModule . '\ChatRoom')) {
            $data = ChatRoom::withoutGlobalScopes()
                ->where('status', 'active')
                ->where(function (Builder $query) use ($keyword) {
                    $query->where('title', 'like', $keyword)
                        ->orWhere('description', 'like', $keyword)
                        ->where('status', 'active');
                })->latest()->paginate();
            return new ChatRoomsResource($data);
        }

        if ($type == 'questions' && class_exists($commiunityModule . '\Question')) {
            $data = Question::withoutGlobalScopes()
            ->where('status', 'approved')
                ->where(function (Builder $query) use ($keyword) {
                    $query->where('text', 'like', $keyword);
                })->latest()->paginate();
            return new QuestionsOldResource($data);
        }

        if ($type == 'products' && class_exists('App\Models\Product')) {
            $data = Product::withoutGlobalScopes()
            ->where('status', 'published')
            ->where(function (Builder $query) use ($keyword) {
                $query->where('title', 'like', $keyword);
            })->latest()->paginate(10);

            return new ProductsResource($data);
        }

        if ($type == 'startups' && class_exists('App\Models\Startup')) {
            $data = Startup::withoutGlobalScopes()
            ->where('status', 'active')
            ->where(function (Builder $query) use ($keyword) {
                $query->where('title', 'like', $keyword);
            })->latest()->paginate(10);

            return new StartupsResource($data);
        }
    }
}
