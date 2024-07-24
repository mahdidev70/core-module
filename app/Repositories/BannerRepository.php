<?php

namespace TechStudio\Core\app\Repositories;

use Illuminate\Support\Facades\Auth;
use TechStudio\Core\app\Http\Resources\BannerResource;
use TechStudio\Core\app\Models\Banner;
use TechStudio\Core\app\Repositories\Interfaces\BannerRepositoryInterface;

class BannerRepository implements BannerRepositoryInterface
{
    public function list($request) 
    {
        $query = Banner::where('type', $request->input('type'));
        
        if ($request->filled('search')) {
            $txt = $request->get('search');
            $query->where(function ($q) use ($txt) {
                $q->where('title', 'like', '%' . $txt . '%');
            });
        }

        if (isset($request->creationDateMax) && $request->creationDateMax != null) {
            $query->whereDate('created_at', '<=', $request->input('creationDateMax'));
        }

        if (isset($request->creationDateMin) && $request->creationDateMin != null) {
            $query->whereDate('created_at', '>=', $request->input('creationDateMin'));
        }

        $banner = $query->orderBy('id', 'DESC')->paginate(10);
        return $banner;
    }

    public function createUpdate($request)
    {
        $banner = Banner::updateOrCreate(
            ['id' => $request['id']],
            [
                'title' => $request['title'],
                'description' => $request['description'],
                'link_url' => $request['linkUrl'],
                'image_url' => $request['imageUrl'],
                'date' => $request['dateOfHolding'],
                'price' => $request['price'],
                'type' => $request['type'],
                'status' => 'published',
            ]
        );
        return $banner;
    }

    public function setStatus($request) 
    {
        Banner::whereIn('id', $request['ids'])
        ->update(
            ['status' => $request['status']]
        );

        $banner = Banner::whereIn('id', $request['ids'])->get();
        return $banner;
    }

    public function common()
    {
        $status = ['published', 'draft', 'deleted'];
        $counts = [
            "all" => Banner::all()->count(),
            "published" => Banner::where('status', 'published')->count(),
            "draft" => Banner::where('status', 'draft')->count(),
            "deleted" => Banner::where('status', 'deleted')->count(),
        ];

        return [
            'status' => $status,
            'counts' => $counts,
        ];
    }

    public function getBannerForHomPage()
    {
        return Banner::where('type', 'banner')->select('title','link_url as linkUrl','image_url as imageUrl')->get();
    }

    public function event() 
    {
        $data = Banner::where('type', 'event')->orderBy('date', 'ASC')->get();
        return BannerResource::collection($data);
    }
}
