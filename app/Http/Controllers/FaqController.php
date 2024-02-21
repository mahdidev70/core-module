<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TechStudio\Core\app\Http\Resources\CategoryResource;
use TechStudio\Core\app\Http\Resources\FaqResource;
use TechStudio\Core\app\Http\Resources\FaqsResource;
use TechStudio\Core\app\Models\Category;
use TechStudio\Core\app\Models\Faq;

class FaqController extends Controller
{
    public function list(Request $request) 
    {
        $query = Faq::where('status', 'active');

        if ($request->filled('search')) {
            $txt = $request->get('search');
            $query->where(function ($q) use ($txt) {
                $q->where('question', 'like', '%' . $txt . '%')
                ->orWhere('answer', 'like', '%' . $txt . '%');
            });
        }

        if (isset($request->categorySlug) && $request->categorySlug != null) {
            $query->whereHas('category', function ($categoryQuery) use ($request) {
                $categoryQuery->where('slug', $request->input('categorySlug'));
            });
        }

        $data = $query->get();
        $isFrequent =Faq::where('status', 'active')->where('is_frequent', 1)->get();
        return [
            'data' => FaqResource::collection($data),
            'isFrequent' => FaqResource::collection($isFrequent)
        ];
    }

    public function common() 
    {
        $faqModel = new Faq();
        $categories = Category::where('table_type', get_class($faqModel))
        ->whereHas('faq')
        ->get();

        return CategoryResource::collection($categories);
    }

    public function getFaqData(Request $request)
    {
        $query = Faq::with('category');

        if ($request->filled('search')) {
            $txt = $request->get('search');
            $query->where(function ($q) use ($txt) {
                $q->where('question', 'like', '%' . $txt . '%');
            });
        }

        $sortOrder = 'desc';
        if (isset($request->sortOrder) && ($request->sortOrder ==  'asc' || $request->sortOrder ==  'desc')) {
            $sortOrder = $request->sortOrder;
        }

        if (isset($request->categorySlug) && $request->categorySlug != null) {
            $query->whereHas('category', function ($categoryQuery) use ($request) {
                $categoryQuery->where('slug', $request->input('categorySlug'));
            });
        }

        if (isset($request->status) && $request->status != null) {
            $query->where('status', $request->input('status'));
        }

        $data = $query->orderBy('id', $sortOrder)->paginate(10);
        return new FaqsResource($data);
    }

    public function createUpdate($locale, Request $request)
    {
        $data = Faq::updateOrCreate(
            ['id' => $request['id']],
            [
                'question' => $request['question'],
                'answer' => $request['answers'],
                'category_id' => $request['categoryId'],
                'is_frequent' => $request['isFrequent'],
            ]
        );

        $faq = Faq::where('id', $data->id)->first();

        return new FaqResource($faq);
    }

    public function setStatus($locale, Request $request)
    {
        Faq::whereIn('id', $request['ids'])->update(['status' => $request['status']]);

        return [
            'updateStatus' => $request['ids']
        ];
    }

    public function panelCommon() 
    {
        $modelClass = new Faq();
        $counts = [
            'all' => Faq::count(),
            'active' => Faq::where('status', 'active')->count(),
            'deactive' => Faq::where('status', 'deactive')->count(),
        ];

        $status = ['active', 'deactive'];

        $categories = Category::where('table_type', get_class($modelClass))->get();
        
        return [
            'counts' => $counts,
            'status' => $status,
            'categories' => CategoryResource::collection($categories),
        ];
    }
}
