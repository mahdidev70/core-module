<?php

namespace TechStudio\Core\app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use TechStudio\Lms\app\Models\Course;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Core\app\Models\UserProfile;
use TechStudio\Community\app\Models\ChatRoom;
use TechStudio\Community\app\Models\Question;
use TechStudio\Core\app\Services\Search\SearchService;
use TechStudio\Blog\app\Http\Resources\ArticleResource;
use TechStudio\Core\app\Http\Resources\ArticlesResource;
use TechStudio\Lms\app\Http\Resources\CoursePreviewResource;
use TechStudio\Community\app\Http\Resources\QuestionResource;
use TechStudio\Community\app\Http\Resources\ChatRoomResource;

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
            $data = Article::withoutGlobalScopes()->where('title', 'like', $keyword)
                ->orWhere('summary', 'like', $keyword)
                ->orWhere('content', 'like', $keyword)
                ->where('status', 'published')->paginate();
            return new ArticlesResource($data);
        }

        if ($type == 'courses' && class_exists($lmsModule . '\Course')) {
            $data = Course::withoutGlobalScopes()->where('title', 'like', $keyword)
                ->orWhere('description', 'like', $keyword)
                ->orWhere('faq', 'like', $keyword)
                ->where('status', 'published')->paginate();
            return new CoursePreviewResource($data);
        }
        
        if ($type == 'rooms' && class_exists($commiunityModule . '\ChatRoom')) {
            $data = ChatRoom::withoutGlobalScopes()->where('title', 'like', $keyword)
                ->orWhere('description', 'like', $keyword)
                ->where('status', 'active')->paginate();
            return new ChatRoomResource($data);
        }

        if ($type == 'questions' && class_exists($commiunityModule . '\Question')) {
            $data = Question::withoutGlobalScopes()->where('text', 'like', $keyword)
                ->where('status', 'approved')->paginate();
            return new QuestionResource($data);
        }
    }
}
