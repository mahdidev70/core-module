<?php

namespace TechStudio\Core\app\Repositories\Interfaces;

interface TroubleshootingReportRepositoryInterface
{
    public function store(array $parameters): void;
}