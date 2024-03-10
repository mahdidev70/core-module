<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TechStudio\Core\app\Http\Resources\FollowsResource;
use TechStudio\Core\app\Repositories\Interfaces\FollowRepositoryInterface;

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
      return $data;
   }

   public function followersList(Request $request) 
   {
      $data = $this->repository->followersList($request);
      return new FollowsResource($data);
   }

   public function followingList(Request $request) 
   {
      $data = $this->repository->followingList($request);
      return new FollowsResource($data);
   }
}
