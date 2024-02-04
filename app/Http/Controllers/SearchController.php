<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TechStudio\Blog\app\Http\Resources\ArticleResource;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Core\app\Models\UserProfile;

class SearchController extends Controller
{
    public function searchUser(Request $request)
    {
        $txt = $request->query->get('query');
        $res = [];
        if ($txt){
            $users = UserProfile::where('status','active')->where(function($q) use($txt){
                $q->Where('first_name', 'like', '% '.$txt.'%')
                ->orWhere('last_name', 'like', '% '.$txt.'%');
            })->take(10)->get(['first_name','last_name','id','avatar_url']);

            $res = $users->map(fn($user) => [
                'id' => $user->id,
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
                // ->orWhere('content', 'like', '%' . $keyword . '%')
                ->take(5)->get()->map(function ($item) {
                    $item['type'] = 'article';
                    return $item;
                  });;
            return response()->json(ArticleResource::collection($result));
        }
    }
}
