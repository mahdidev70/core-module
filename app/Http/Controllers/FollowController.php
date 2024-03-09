<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TechStudio\Lms\app\Repositories\Interfaces\FollowRepositoryInterface;

class FollowController extends Controller
{
    private FollowRepositoryInterface $repository;
    public function __construct(FollowRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
   public function storeRemove(Request $request) 
   {
        $data = $this->repository->storeRemove($request);
   }
}
