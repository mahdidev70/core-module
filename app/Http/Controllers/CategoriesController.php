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
    public function createCategory($local, Category $category, Request $request)
    {
        $checkCategory = Category::where('title', $request->title)->first();

        if ($checkCategory) {
            return response()->json(['message' => 'این عنوان قبلا انتخاب شده'], 409);
        }else {
            $validatedData = $request->validate([
                'title' => 'required|string',
                'slug' => 'required|string',
                'description' => 'nullable|string',
            ]);

            $article = new Article();
        
            $category = new Category();
            $category->title = $validatedData['title'];
            // $category->slug = SlugGenerator::transform($validatedData['title']);
            $category->slug = $validatedData['slug'];
            $category->description = $validatedData['description'];
            $category->table_type = get_class($article);
            $category->save();
        
            $articleCount = $category->articles()->count();
        
            $responseData = [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'description' => $category->description,
                'status' => 'active',
                'articleCount' => $articleCount,
                'bookmarksCount' => 0,
                'commentsCount' => 0,
                'viewsCount' => 0,
            ];
        
            return response()->json($responseData);
        }
        
    }

    public function listCategory(Request $request)
    {

        if($request->filled('search')){
            $txt = $request->get('search');

            $query = Category::where(function($q) use($txt){
                $q->where('title','like', '%'.$txt)
                 ->orWhere('title', 'like', '% '.$txt.'%')
                  ->orWhere('title','like',$txt.'%');
            });

            $categories = $query->take(10)->get(['title', 'slug']);

            $categories = $query->paginate(10);

            return $categories;

        }

        $article = new Article();

        $categories= Category::where('table_type', get_class($article))->withCount('articles')->paginate(10);

        $articles = Article::with('categories');

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
                    //ToDo Core
                    'commentsCount' => $category->articles->map(function($article){
                        return $article->bookmarks()->count();
                    })->sum(),

                    'bookmarksCount' => $category->articles->map(function($article){
                        return $article->comments()->count();
                    })->sum(),
                    'viewsCount' => $category->articles->pluck('viewsCount')->sum(),
                ];
            }),
        ];

        return $data;
    }

    public function getCommonListCategory()
    {
        $data = [
            'counts' => [
                'all' => Category::count(),
                'active' =>Category::where('status', 'active')->count(),
                'hidden' => Category::where('status', 'hidden')->count(),
                'delete' =>Category::where('status', 'deleted')->count(),
            ]
        ];

        return $data;
    }

    public function updateCategory($local, Category $category, Request $request)
    {

        $validatedData = $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string',
            'slug' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,hidden,deleted',
        ]);

        $category = Category::where('id', $validatedData['id'])->firstOrFail();

        $category->title = $validatedData['title'];
        // $category->slug = SlugGenerator::transform($validatedData['newTitle']);
        $category->slug = $validatedData['slug'];
        $category->description = $validatedData['description'];
        $category->status = $validatedData['status'];
        $category->save();
    
        $updatedCategory = Category::where('id', $validatedData['id'])->first();
    
        $articleCount = $updatedCategory->articles()->count();
    
        $responseData = [
            'id' => $category->id,
            'slug' => $category->slug,
            'title' => $category->title,
            'description' => $category->description,
            'status' => $category->status,
            'articleCount' => $articleCount,
        ];
    
        return response()->json($responseData);

    }

    public function updateCategoryStatus($local, Category $category, Request $request) 
    {
        $validatedData = $request->validate([
            'status' => 'required|in:active,hidden,deleted',
            'ids' => 'required|array',
        ]);

        $ids = collect($validatedData['ids']);

        $category->whereIn('id', $ids)->update(['status' => $validatedData['status']]);

        return [
            'updateCategory' => $ids,
        ];
    }


    public function deleteCategory(Request $request, Category $category)
    {

        $query = Category::whereIn('id', $request->ids);
        $ids = $query->pluck('id');

        $query->delete();

        return [
            'deletedCategories' => $ids,
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
                //ToDo Core
                'slug' => SlugGenerator::transform($request['title']),
                'description' => $request['description'],
                'table_type' => get_class($course),
            ]
        );

        return $category->id;
    }

    public function getCourseCategoyCommon()
    {
        $course = new Course();
        
        $counts = Category::where('table_type', get_class($course));

        $counts =[
            'all' => $counts->count(),
            'delete' => $counts->where('status', 'deleted')->count(),
            'active' => $counts->where('status', 'active')->count(),
            'hidden' => $counts->where('status', 'hidden')->count(),
        ];

        return $counts;
    }

}
