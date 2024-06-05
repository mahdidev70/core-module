<?php

namespace TechStudio\Core\app\Repositories;

use Illuminate\Support\Facades\Auth;
use TechStudio\Core\app\Http\Resources\BannerResource;
use TechStudio\Core\app\Models\Banner;
use TechStudio\Core\app\Repositories\Interfaces\BannerRepositoryInterface;

class BannerRepository implements BannerRepositoryInterface
{
    public function list() 
    {
        $data = Banner::orderBy('id', 'DESC')->paginate(10);
        return $data;
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
                'type' => $request['type'],
                'status' => 'draft',
            ]
        );
        return $banner;
    }

    public function setStatus($request) 
    {
        
    }

    public function getBannerForHomPage()
    {
        return Banner::where('type', 'banner')->select('title','link_url as linkUrl','image_url as imageUrl')->get();
    }

    public function event() 
    {
        $data = Banner::where('type', 'evant')->orderBy('id', 'DESC')->get();
        return BannerResource::collection($data);
    }
}
