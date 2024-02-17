<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use TechStudio\Blog\app\Http\Resources\ArticlesResource;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Blog\app\Services\Article\ArticleService;
use TechStudio\Community\app\Http\Resources\ChatRoomsResource;
use TechStudio\Community\app\Models\ChatRoom;
use TechStudio\Lms\app\Http\Controllers\CourseController;
use TechStudio\Lms\app\Http\Controllers\HomeController;
use TechStudio\Lms\app\Repositories\CategoryLmsRepository;
use TechStudio\Lms\app\Repositories\CommentRepository;

class LandingController extends Controller
{
    public function __construct(protected ArticleService $articleService,)
    {}

    public function first(Request $request) 
    {
        $language = App::currentLocale();

        $articleQuery = Article::with('category')->where('language', $language)->where('type', 'article')->whereNotNull('publicationDate');
        $articles = $articleQuery->orderBy('publicationDate', 'DESC')->paginate(10);

        $homeController = new HomeController(
            new CategoryLmsRepository(), 
            new CommentRepository()     
        );

        $courses = $homeController->index();

        $chatRoomQuery = ChatRoom::where('status', 'active');
        $chatRooms = $chatRoomQuery->orderBy('id', 'DESC')->paginate(10);

        return [
            'articles' => new ArticlesResource($articles),
            'courses' => $courses,
            'chatRooms' => new ChatRoomsResource($chatRooms),
        ];
    }
}
