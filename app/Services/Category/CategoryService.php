<?php

namespace TechStudio\Core\app\Services\Category;

use TechStudio\Blog\app\Models\Article;
use TechStudio\Core\app\Models\Category;
use Illuminate\Support\Facades\App;


class CategoryService
{
    public function getCategoriesForFilter($class)
    {
        $language = App::currentLocale();

        $categories = Category::select('slug','title')->where('table_type',get_class($class))->where('language', $language)->get()->toArray();
        $all = [
            "slug" => "all",
            "title" => "همه"];
        array_unshift($categories, $all);

        return $categories;
    }
}
