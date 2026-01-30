<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackorderService;
use Illuminate\Http\Request;

class BackorderController extends Controller
{
    private BackorderService $backorderService;

    public function __construct(BackorderService $backorderService)
    {
        $this->backorderService = $backorderService;
    }

    public function index(Request $request)
    {
        $backorders = $this->backorderService->listBackorders(
            $request->project_id,
            $request->supplier_id,
            session('company_id')
        );

        return view('admin.backorders.index', compact('backorders'));
    }
}
