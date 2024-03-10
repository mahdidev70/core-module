<?php

namespace TechStudio\Core\app\Services\Search;

use TechStudio\Blog\app\Models\Article;
use TechStudio\Core\app\Models\Tag;
use TechStudio\Core\app\Models\UserProfile;

class SearchService
{
    public static function search($type, $keyword)
    {
        $data = [];
        if ($type == 'article') {
            $data = Article::where('title', 'like', '%' . $keyword . '%')
                ->orWhere('summary', 'like', '%' . $keyword . '%')
                ->orWhere('content', 'like', '%' . $keyword . '%')
                ->paginate();
        }
        if ($type == 'user') {
            $data = UserProfile::where('first_name', 'like', '%' . $keyword . '%')
            ->orWhere('last_name', 'like', '%' . $keyword . '%')
            ->paginate();
        }
        if ($type == 'tag') {
            $data = Tag::where('title', 'like', '%' . $keyword . '%')
            ->paginate();
        }
        return $data;
    }
}
