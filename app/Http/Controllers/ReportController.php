<?php

namespace TechStudio\Core\app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use TechStudio\Core\app\Models;
use TechStudio\Core\app\Models\Report;

class ReportController extends Controller
{
    public function list()
    {
        return Report::get();
    }
}
