<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        $reports = \App\Models\Report::with(['post', 'user'])->latest()->get();
        return view('admin.reports', compact('reports'));
    }

    // other methods like resolve(), resolveOrphan(), etc.
}
