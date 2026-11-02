<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOfficeRequest;
use App\Models\Office;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OfficeController extends Controller
{
    public function index(): View
    {
        $offices = Office::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('offices.index', compact('offices'));
    }

    public function update(UpdateOfficeRequest $request, Office $office): RedirectResponse
    {
        $office->update($request->validated());

        return redirect()
            ->route('offices.index')
            ->with('success', "Data {$office->name} berhasil diperbarui.");
    }
}
