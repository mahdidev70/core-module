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
        ->select('id', 'title', 'slug', 'description', 'status', 'language')
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

        if (isset($request->status) && $request->status != null) {
            $query->where('status', $request->input('status'));
        }

        $data = $query->paginate();
        return new FaqsResource($data);
    }

    public function createUpdate($locale, Request $request)
    {
        $data = Faq::updateOrCreate(
            ['id' => $request['id']],
            [
                'question' => $request['question'],
                'answer' => $request['answer'],
                'category_id' => $request['categoryId'],
                'is_frequent' => $request['isFrequent'],
            ]
        );

        return new FaqResource($data);
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
