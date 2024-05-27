<?php

namespace TechStudio\Core\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TechStudio\Core\app\Http\Requests\TroubleshootingReport\StoreRequest;
use TechStudio\Core\app\Repositories\Interfaces\TroubleshootingReportRepositoryInterface;


class TroubleShootingReportController extends Controller
{
    private TroubleshootingReportRepositoryInterface $troubleshootingReportRepository;

    public function __construct(TroubleshootingReportRepositoryInterface $troubleshootingReportRepository)
    {
        $this->troubleshootingReportRepository = $troubleshootingReportRepository;
    }

    public function store(StoreRequest $request)
    {
        $this->troubleshootingReportRepository->store($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'عملیات با موفقیت انجام شد.'
        ], 200);
    }

}
