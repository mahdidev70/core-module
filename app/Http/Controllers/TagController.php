<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use TechStudio\Core\app\Models\Tag;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Core\app\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TechStudio\Core\app\Helper\SlugGenerator;

class TagController extends Controller
{
    public function listTags(Request $request)
    {
        $query = Tag::with('articles');

        if ($request->filled('search')) {
            $txt = $request->get('search');
            $query = $query->where(function($q) use($txt) {
                $q->where('title', 'like', '%'.$txt)
                    ->orWhere('title', 'like', '% '.$txt.'%')
                    ->orWhere('title', 'like', $txt.'%');
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

            }elseif ($request->sortKey == 'bookmars') {
                $query->leftJoin('taggables', function ($join) {
                $join->on('tags.id', '=', 'taggables.tag_id')
                    ->where('taggables.taggable_type', '=', 'TechStudio\\Blog\\app\\Models\\Article');
            })
            ->leftJoin('articles', 'articles.id', '=', 'taggables.taggable_id')
            ->leftJoin('bookmarks', function ($join) {
                $join->on('articles.id', '=', 'bookmarks.bookmarkable_id')
                    ->where('bookmarks.bookmarkable_type', '=', 'TechStudio\\Blog\\app\\Models\\Article');
            })
            ->groupBy('tags.id')
            ->orderBy(DB::raw('COUNT(bookmarks.id)'), $sortOrder);
            }elseif ($request->sortKey == 'comments') {
                $query->leftJoin('taggables', function ($join) {
                    $join->on('tags.id', '=', 'taggables.tag_id')
                        ->where('taggables.taggable_type', '=', 'TechStudio\\Blog\\app\\Models\\Article');
                    })
                    ->leftJoin('articles', 'articles.id', '=', 'taggables.taggable_id')
                    ->leftJoin('comments', function ($join) {
                        $join->on('articles.id', '=', 'comments.commentable_id')
                            ->where('comments.commentable_type', '=', 'TechStudio\\Blog\\app\\Models\\Article');
                    })->groupBy('tags.id')->orderBy(DB::raw('COUNT(comments.id)'), $sortOrder);
            }
        }

        $tag = $query->withCount('articles')->orderBy('id', $sortOrder)->paginate(10);

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

    }

    public function getCommonListTag()
    {
        $counts = [
            'all' => Tag::count(),
            'active' =>Tag::where('status', 'active')->count(),
            'hidden' => Tag::where('status', 'hidden')->count(),
            'delete' =>Tag::where('status', 'deleted')->count(),
    ];

    $status = ['active', 'hidden', 'deleted'];

    return[
        'counts' => $counts,
        'status' => $status,
    ];
    }

    public function createUpdateTags($local, Tag $tag, Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string',
            'slug' => 'string',
            'description' => 'nullable|string',
            'status' => 'in:active,hidden,deleted',
        ]);

        $tagData = [
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'status' => $validatedData['status'],
        ];

        if (array_key_exists('slug', $validatedData)) {
            $tagData['slug'] = $validatedData['slug'];
        } else {
            $tagData['slug'] = SlugGenerator::transform($validatedData['title']);
        }

        $tag = Tag::updateOrCreate(['id' => $validatedData['id']],$tagData);

        return [
            'id' => $tag->id,
            'title' => $tag->title,
            'slug' => $tag->slug,
            'description' => $tag->description,
            'status' => $tag->status,
            'articleCount' => $tag->articles->count(),
        ];
    }

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
}
