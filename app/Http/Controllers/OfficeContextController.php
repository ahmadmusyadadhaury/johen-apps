<?php

namespace App\Http\Controllers;

use App\Support\OfficeContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OfficeContextController extends Controller
{
    public function update(Request $request, OfficeContext $officeContext): RedirectResponse
    {
        $validated = $request->validate([
            'office_id' => ['required', 'string', 'max:30'],
        ]);

        abort_unless(
            $officeContext->select($request->user(), $validated['office_id']),
            403,
            'Anda tidak memiliki akses ke kantor ini.',
        );

        $request->session()->forget('division_menu');

        return back()->with('success', 'Konteks kantor diubah ke '.$officeContext->label().'.');
    }
}
