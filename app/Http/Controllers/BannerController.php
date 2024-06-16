<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TechStudio\Core\app\Http\Requests\CreateUpdateBannerRequest;
use TechStudio\Core\app\Http\Resources\BannerResource;
use TechStudio\Core\app\Http\Resources\BannersResource;
use TechStudio\Core\app\Repositories\Interfaces\BannerRepositoryInterface;

class BannerController extends Controller
{
    private BannerRepositoryInterface $repository;
    public function __construct(BannerRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createUpdate(CreateUpdateBannerRequest $request) 
    {
        $data = $this->repository->createUpdate($request);
        return new BannerResource($data);
    }

    public function list(Request $request) 
    {
        $data = $this->repository->list($request);
        return new BannersResource($data);
    }

    public function setStatus(Request $request) 
    {
        $data = $this->repository->setStatus($request);
        return BannerResource::collection($data);
    }

    public function common() 
    {
        $data = $this->repository->common();
        return $data;    
    }
}
