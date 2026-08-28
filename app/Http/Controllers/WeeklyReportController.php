<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class WeeklyReportController extends Controller
{
    public function index()
    {
        if (auth()->user()->isManager()) {
            return view('operasional.weekly-report-coordinator-list');
        }

        return view('operasional.weekly-report');
    }

    public function show(Employee $employee)
    {
        return view('operasional.weekly-report', [
            'employeeId' => $employee->id,
        ]);
    }
}
