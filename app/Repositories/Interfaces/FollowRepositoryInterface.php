<?php

namespace TechStudio\Core\app\Repositories\Interfaces;

interface FollowRepositoryInterface
{
    public function storeRemove($request);
    public function followersList($request);
    public function followingList($request);
}