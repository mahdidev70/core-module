<?php

namespace TechStudio\Core\app\Repositories;

use Illuminate\Support\Facades\Auth;
use TechStudio\Blog\app\Models\Banner;
use TechStudio\Core\app\Repositories\Interfaces\BannerRepositoryInterface;

class BannerRepository implements BannerRepositoryInterface
{
    public function getBannerForHomPage()
    {
        return Banner::select('title','link_url as linkUrl','image_url as imageUrl')->get();
    }
}
