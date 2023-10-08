<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use TechStudio\Core\app\Models\Tag;

use TechStudio\Blog\app\Models\Article; 
use TechStudio\Core\app\Models\Category;


use Illuminate\Http\Request;
use App\Helper\SlugGenerator;


class TagController extends Controller
{
    public function createTags($local, Tag $tag, Request $request)
    {
        $checkTag = Tag::where('title', $request->title)->first();  
        if ($checkTag){
            return response()->json(['message' => 'این عنوان قبلا انتخاب شده'], 409);
        }else {
            return 'asdf';

            $validatedData = $request->validate([
                'title' => 'required|string',
                'slug' => 'required|string',
                'description' => 'nullable|string',
            ]);
    
            $tag = new Tag();
            $tag->title = $validatedData['title'];
            // $tag->slug = SlugGenerator::transform($validatedData['title']);
            $tag->slug = $validatedData['slug'];
            $tag->description = $validatedData['description'];
            $tag->save();
    
            $articleCount = $tag->articles()->count();
    
            $reponseData = [
                'id' => $tag->id,
                'title' => $tag->title,
                'slug' => $tag->slug,
                'description' => $tag->description,
                'status' => 'active',
                'articleCount' => $articleCount,
                'bookmarksCount' => 0,
                'commentsCount' => 0,
                'viewsCount' => 0,
            ];
    
            return response()->json($reponseData);
        }

    }

    public function listTags(Request $request) 
    {
        if ($request->filled('search')) {
            $txt = $request->get('search');
            
            $query = Tag::where(function($q) use($txt) {
                $q->where('title', 'like', '%'.$txt)
                    ->orWhere('title', 'like', '% '.$txt.'%')
                    ->orWhere('title', 'like', $txt.'%');
            });
        
            $tags = $query->take(10)->get(['title', 'slug']);
        
            $tags = $query->paginate(10); 

            return $tags;
        
        }

        $tag = Tag::withCount('articles')->paginate(10);

        $articles = Article::with('tags')->get();

        $data = [
            'total' => $tag->total(),
            'current_page' => $tag->currentPage(),
            'per_page' => $tag->perPage(),
            'last_page' => $tag->lastPage(),
    
            'data' => $tag->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'title' => $tag->title,
                    'slug' => $tag->slug,
                    'description' => $tag->description,
                    'status' => $tag->status,
                    'articleCount' => $tag->articles_count, 
                    'bookmarksCount' => $tag->articles->map(function ($article) {
                        return $article->bookmarks()->count();
                    })->sum(),
                    'commentsCount' => $tag->articles->map(function ($article){
                        return $article->comments()->count();
                    })->sum(),
                    'viewsCount' => $tag->articles->pluck('viewsCount')->sum(),
                ];
            }),
        ];

        return $data;
        $query = Tag::withCount('articles');

    if ($request->filled('search')) {
        $txt = $request->input('search');

        $query->where(function($q) use($txt) {
            $q->where('title', 'like', '%' . $txt)
              ->orWhere('title', 'like', '% ' . $txt . '%')
              ->orWhere('title', 'like', $txt . '%');
        });
        $tags = $query->take(10)->get(['title', 'slug']);
        
            $tags = $query->paginate(10); 

            return $tags;
    }

    $sortBy = $request->input('sort_by', 'title');
    $sortOrder = $request->input('sort_order', 'asc');

    $validSortColumns = ['title', 'viewsCount', 'commentsCount', 'bookmarksCount'];

    if (!in_array($sortBy, $validSortColumns)) {
        $sortBy = 'title';
    }

    $sortOrder = strtolower($sortOrder) === 'desc' ? 'desc' : 'asc';

    $query->orderBy($sortBy, $sortOrder);
    if ($request->has('sort')) {
        if ($request->sort == 'bookmarks') {
            $query->orderByDesc('bookmarks_count');
        } elseif ($request->sort == 'views') {
            $query->orderByDesc('viewsCount');
        } elseif ($request->sort == 'comments') {
            $query->withCount('comments')->orderByDesc('comments_count');
        }
    } 

    $tags = $query->paginate(10);

    $data = [
        'total' => $tags->total(),
        'current_page' => $tags->currentPage(),
        'per_page' => $tags->perPage(),
        'last_page' => $tags->lastPage(),
        'data' => $tags->map(function ($tag) {
            return [
                'id' => $tag->id,
                'title' => $tag->title,
                'slug' => $tag->slug,
                'description' => $tag->description,
                'status' => $tag->status,
                'articleCount' => $tag->articles_count,
                'bookmarksCount' => $tag->articles->map(function ($article) {
                    return $article->bookmarks()->count();
                })->sum(),
                'commentsCount' => $tag->articles->map(function ($article){
                    return $article->comments()->count();
                })->sum(),
                'viewsCount' => $tag->articles->pluck('viewsCount')->sum(),
            ];
        })
    ];

    return $data;

    }

    public function getCommonListTag()
    {
        $data = [
            'counts' => [
                'all' => Tag::count(),
                'active' =>Tag::where('status', 'active')->count(),
                'hidden' => Tag::where('status', 'hidden')->count(),
                'delete' =>Tag::where('status', 'deleted')->count(),
            ]
        ];

        return $data;
    }

    // public function updateTags($local, Tag $tag, Request $request) 
    // {
    //     $validatedData = $request->validate([
    //         'id' => 'required|integer',
    //         'title' => 'required|string',
    //         'slug' => 'required|string',
    //         'description' => 'nullable|string',
    //         'status' => 'required|in:active,hidden,deleted',
    //     ]);

    //     $tag = Tag::where('id', $validatedData['id'])->firstOrFail();

    //     $tag->title = $validatedData['title'];
    //     // $tag->slug = SlugGenerator::transform($validatedData['newTitle']);.
    //     $tag->slug = $validatedData['slug'];
    //     $tag->description = $validatedData['description'];
    //     $tag->status = $validatedData['status'];
    //     $tag->save();
    
    //     $updatedTag = Tag::where('id', $validatedData['id'])->first();
    
    //     $articleCount = $updatedTag->articles()->count();
    
    //     $responseData = [
    //         'id' => $tag->id,
    //         'title' => $tag->title,
    //         'slug' => $tag->slug,
    //         'description' => $tag->description,
    //         'status' => $tag->status,
    //         'articleCount' => $articleCount,
    //     ];
    
    //     return response()->json($responseData);
    // }

    public function updateTagsStatus(Request $request, Tag $tag)
    {
        $validatedData = $request->validate([
            'status' =>'required|in:active,hidden,deleted',
            'ids' => 'required|array',
        ]);

        $ids = collect($validatedData['ids']);

        $tag->whereIn('id', $ids)->update(['status' => $validatedData['status']]);

        return [
            'updateTags' => $ids,
        ];
    }

//     // public function deleteTags(Tag $tag, Request $request) 
//     // {
//     //     $query = Tag::whereIn('id', $request->ids);
//     //     $ids = $query->pluck('id');

//     //     $query->delete();

//     //     return [
//     //         'deletedTags' => $ids,
//     //     ];
//     // }
}
