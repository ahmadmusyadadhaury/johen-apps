<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class ActivityCompetitorController extends Controller
{
    public function index()
    {
        if (auth()->user()->isManager()) {
            return view('operasional.activity-competitor-coordinator-list');
        }

        return view('operasional.activity-competitor');
    }

    public function show(Employee $employee)
    {
        abort_unless(auth()->user()->isManager(), 403);

        return view('operasional.activity-competitor', [
            'employeeId' => $employee->id,
        ]);
    }
}
