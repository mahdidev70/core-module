<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TechStudio\Core\app\Http\Resources\FaqResource;
use TechStudio\Core\app\Http\Resources\FaqsResource;
use TechStudio\Core\app\Models\Category;
use TechStudio\Core\app\Models\Faq;

class FaqController extends Controller
{
    public function list($locale, Request $request) 
    {
        $data = Faq::where('status', 'active')->get();
        return FaqResource::collection($data);
    }

    public function common() 
    {
        $faqModel = new Faq();
        $categories = Category::where('table_type', get_class($faqModel))
        ->select('id', 'title', 'slug', 'description', 'status', 'language')
        ->whereHas('faq')
        ->get();

        return $categories;
    }

    public function getFaqData()
    {
        $data = Faq::paginate(10);
        return new FaqsResource($data);
    }

    public function createUpdate($locale, Request $request)
    {
        $data = Faq::updateOrCreate(
            ['id' => $request['id']],
            [
                'question' => $request['question'],
                'answer' => $request['answer'],
                'category_id' => $request['category'],
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
        $counts = [
            'all' => Faq::count(),
            'active' => Faq::where('status', 'active')->count(),
            'deactive' => Faq::where('status', 'deactive')->count(),
        ];

        $status = ['active', 'deactive'];

        return [
            'counts' => $counts,
            'status' => $status
        ];
    }
}
