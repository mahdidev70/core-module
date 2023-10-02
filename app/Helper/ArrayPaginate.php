<?php

namespace TechStudio\Core\app\Helper;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ArrayPaginate {
    public static function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $result = new LengthAwarePaginator(
            array_values($items->forPage($page, $perPage)->toArray()),
            $items->count(),
            $perPage,
            $page,
            $options
        );
        return $result;
    }
}
