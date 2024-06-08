<?php

namespace TechStudio\Core\app\Repositories\Interfaces;

interface BannerRepositoryInterface
{
    public function getBannerForHomPage();
    public function list($request);
    public function createUpdate($request);
    public function setStatus($request);
    public function event();
}