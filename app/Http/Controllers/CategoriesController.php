<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Core\app\Models\Category;
use TechStudio\Core\app\Helper\SlugGenerator;
use TechStudio\Lms\app\Models\Course;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    public function listCategory(Request $request)
    {
        $articleModel = new Article();

        $query= Category::where('table_type', get_class($articleModel))->withCount('articles');

        if($request->filled('search')){
            $txt = $request->get('search');
            $query->where(function($q) use($txt){
                $q->where('title','like', '%'.$txt)
                ->orWhere('title', 'like', '% '.$txt.'%')
                ->orWhere('title','like',$txt.'%');
            });
        }

        if (isset($request->status) && $request->status != null) {
            $query->where('status', $request->input('status'));
        }

        $sortOrder= 'desc';
        if (isset($request->sortOrder) && ($request->sortOrder ==  'asc' || $request->sortOrder ==  'desc')) {
            $sortOrder = $request->sortOrder;
        }

        if ($request->has('sortKey')) {
            if ($request->sortKey == 'views') {
                $query->withCount(['articles as viewCount_sum' => function ($query) {
                    $query->select(DB::raw('sum(viewsCount)'));
                }])->orderBy('viewCount_sum', $sortOrder);
            }elseif ($request->sortKey == 'bookmarks') {
                    $query->leftJoin('articles', 'categories.id', '=', 'TechStudio\\Blog\\app\\Models\\Article')
                    ->leftJoin('bookmarks', function ($join) {
                        $join->on('articles.id', '=', 'bookmarks.bookmarkable_id')
                            ->where('bookmarks.bookmarkable_type', '=', 'TechStudio\\Blog\\app\\Models\\Article');
                    })->groupBy('categories.id')->orderBy(DB::raw('COUNT(bookmarks.id)'), $sortOrder);
            }elseif ($request->sortKey == 'comments') {
               $query->leftJoin('articles', 'categories.id', '=', 'articles.category_id')
                    ->leftJoin('comments', function ($join) {
                        $join->on('articles.id', '=', 'comments.commentable_id')
                            ->where('comments.commentable_type', '=', 'TechStudio\\Blog\\app\\Models\\Article');
                    }) ->groupBy('categories.id')->orderBy(DB::raw('COUNT(articles_count)'), $sortOrder);
            }
        }else{
            $query->orderByDesc('created_at');
        }

        $categories = $query->paginate(10);

        $data = [
            'total' => $categories->total(),
            'current_page' => $categories->currentPage(),
            'per_page' => $categories->perPage(),
            'last_page' => $categories->lastPage(),
            'data' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'slug' => $category->slug,
                    'title' => $category->title,
                    'status' => $category->status,
                    'description' => $category->description,
                    'articleCount' => $category->articles_count,
                    'commentsCount' => $category->articles->map(function($article){
                        return $article->comments()->count();
                    })->sum(),
                    'bookmarksCount' => $category->articles->map(function($article){
                        return $article->bookmarks()->count();
                    })->sum(),
                    'viewsCount' => $category->articles->pluck('viewsCount')->sum(),
                ];
            }),
        ];

        return $data;
    }

    public function getCommonListCategory()
    {
        $articleModel = new Article();

        $category = Category::where('table_type', get_class($articleModel));

        $counts = [
                'all' => $category->count(),
                'active' =>$category->where('status', 'active')->count(),
                'hidden' => $category->where('status', 'hidden')->count(),
                'deleted' =>$category->where('status', 'deleted')->count(),
        ];

        $status = ['active', 'hidden', 'deleted'];

        return [
            'counts' => $counts,
            'status' => $status,
        ];
    }

    public function createUpdateCategory($local, Category $category, Request $request)
    {
        $articleModel = new Article();

        $category = Category::updateOrCreate(
            ['id' => $request['id']],
            [
                'title' => $request['title'],
                'slug' => $request['slug'] ? $request['slug'] : SlugGenerator::transform($request['title']) ,
                'description' => $request['description'],
                'table_type' => get_class($articleModel),
                'status' => $request['status'],
            ]
        );

        return [
            'id' => $category->id,
            'title' => $category->title,
            'slug' => $category->slug,
            'description' => $category->description,
            'articleCount' => $category->articles->count(),
            'status' => $category->status,
        ];

    }

    public function updateCategoryStatus($local, Category $category, Request $request)
    {
        $category->whereIn('id', $request['ids'])->update(['status' => $request['status']]);

        return [
            'updateCategory' =>  $request['ids'],
        ];
    }

    public function getCourseCategoryList(Request $request)
    {
        $course = new Course();

        $query = Category::where('table_type', get_class($course));

        if ($request->filled('search')) {
            $txt = $request->get('search');

            $query->where(function ($q) use ($txt) {
                $q->where('title', 'like', '%' . $txt . '%');
            });
        }

        if ($request->has('sort')) {
            if ($request->sort == 'coursesCount') {
                $query->withCount('courses')->orderBy('courses_count', 'desc');
            }elseif ($request->sort == 'studentsCount') {
                $query->with(['courses' => function ($q) {
                    $q->withCount('students');
                }])->get()->sortByDesc(function ($category) {
                    return $category->courses->sum('students_count');
                });
            }
        }else{
            $query->orderBy('created_at', 'desc');
        }

        $categories = $query->paginate(10);

        $categoriesData = $categories->map(function ($category){
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'description' => $category->description,
                'courseCount' => $category->courses->count(),
                'studentsCount' => $category->courses->sum(function ($course) {
                    return $course->students->count();
                }),
                'status' => $category->status,
            ];
        });

        return [
            'total' => $categories->total(),
            'current_page' => $categories->currentPage(),
            'per_page' => $categories->perPage(),
            'last_page' => $categories->lastPage(),
            'data' => $categoriesData,

        ];

    }

    public function editCreateCategoryCourse(Request $request)
    {
        $course = new Course();

        $category = Category::updateOrCreate(
            ['id' => $request['id']],
            [
                'title' => $request['title'],
                'slug' => $request['slug'] ? $request['slug'] : SlugGenerator::transform($request['title']) ,
                'description' => $request['description'],
                'table_type' => get_class($course),
                'status' => $request['status'] ? $request['status'] : 'active',
            ]
        );

        return [
            'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'description' => $category->description,
                'courseCount' => $category->courses->count(),
                'studentsCount' => $category->courses->sum(function ($course) {
                    return $course->students->count();
                }),
                'status' => $category->status,
        ];
    }

    public function getCourseCategoyCommon()
    {
        $courseModel = new Course();

        $counts = Category::where('table_type', get_class($courseModel));

        $counts =[
            'all' => $counts->count(),
            'delete' => $counts->where('status', 'deleted')->count(),
            'active' => $counts->where('status', 'active')->count(),
            'hidden' => $counts->where('status', 'hidden')->count(),
        ];

        return $counts;
    }

}
