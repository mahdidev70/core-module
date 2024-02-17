<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use stdClass;
use TechStudio\Blog\app\Http\Resources\ArticleResource;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Blog\app\Services\Article\ArticleService;
use TechStudio\Community\app\Http\Resources\ChatRoomResource;
use TechStudio\Community\app\Models\ChatRoom;
use TechStudio\Lms\app\Http\Controllers\CourseController;
use TechStudio\Lms\app\Http\Controllers\HomeController;
use TechStudio\Lms\app\Http\Resources\CategoryCoursesResource;
use TechStudio\Lms\app\Http\Resources\HomePageResource;
use TechStudio\Lms\app\Repositories\CategoryLmsRepository;
use TechStudio\Lms\app\Repositories\CommentRepository;
use TechStudio\Lms\app\Repositories\Interfaces\CategoryLmsRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\CommentRepositoryInterface;

class LandingController extends Controller
{
    private CategoryLmsRepositoryInterface $categoryRepository;
    private CommentRepositoryInterface $commentRepository;

    public function __construct(protected ArticleService $articleService, CategoryLmsRepositoryInterface $categoryRepository, CommentRepositoryInterface $commentRepository,)
    {
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
    }

    public function first(Request $request) 
    {
        $language = App::currentLocale();

        $articleQuery = Article::with('category')->where('language', $language)->where('type', 'article')->whereNotNull('publicationDate');
        $articles = $articleQuery->orderBy('publicationDate', 'DESC')->take(3)->get();

        $data = $this->categoryRepository->getCategoriesWithCourses();
        $courses = CategoryCoursesResource::collection($data);

        $chatRoomQuery = ChatRoom::where('most_popular', 1)->where('status', 'active');
        $chatRooms = $chatRoomQuery->orderBy('id', 'DESC')->get();

        return [
            'articles' => ArticleResource::collection($articles),
            'categories' => $courses,
            'chatRooms' => ChatRoomResource::collection($chatRooms),
        ];
    }
}
