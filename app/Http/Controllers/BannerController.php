<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
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

    public function list() 
    {
        $data = $this->repository->list();
        return new BannersResource($data);
    }

    public function setStatus(Request $request) 
    {

    }

}
