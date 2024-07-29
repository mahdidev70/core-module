<?php

namespace TechStudio\Core\app\Helper;

class SlugGenerator {
    public static function transform(string $title, string $separator = '-')
    {
        $titleArray = explode(' ',$title);
        if (count($titleArray) == 1){
            // one word with fake data like "sasdadasdsasdadasdsasdadasdsasdada" return error
            if (strlen($title) > 34){
                return false;
            }
        }
        $title = implode(' ',array_slice($titleArray, 0, 8, true));
        $title = trim($title);
        $title = mb_strtolower($title, 'UTF-8');

        $title = str_replace('‌', $separator, $title);

        $title = preg_replace(
            '/[^a-z0-9_\s\-اآؤئبپتثجچحخدذرزژسشصضطظعغفقكکگلمنوةيإأۀءهی۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩]/u',
            '',
            $title
        );

        $title = preg_replace('/[\s\-_]+/', ' ', $title);
        $title = preg_replace('/[\s_]/', $separator, $title);
        $title = trim($title, $separator);

        return $title;
    }

    public function unique($slug): string
    {
        return $slug . now()->timestamp;
    }
}
