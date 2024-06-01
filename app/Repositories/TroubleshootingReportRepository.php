<?php

namespace TechStudio\Core\app\Repositories;

use TechStudio\Core\app\Models\TroubleshootingReport;
use Illuminate\Support\Facades\Auth;
use TechStudio\Core\app\Repositories\Interfaces\TroubleshootingReportRepositoryInterface;

class TroubleshootingReportRepository implements TroubleshootingReportRepositoryInterface
{
    private TroubleshootingReport $troubleshootingReport;

    public function __construct(private TroubleshootingReport $model)
    {
        $this->troubleshootingReport = $model;
    }

    public function store(array $parameters): void
    {
        $this->troubleshootingReport->query()->create([
            'user_id' => $parameters['user_id'],
            'report' => $parameters['report'],
            'reportable_id' => $parameters['reportableId'],
            'reportable_type' => $parameters['reportableType'],
        ]);
    }
}
