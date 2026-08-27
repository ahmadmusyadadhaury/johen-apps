<?php

namespace App\Http\Controllers;

use App\Models\ItProject;
use Illuminate\Http\Request;

class ProjectItController extends Controller
{
    public function index()
    {
        $projects = ItProject::with('creator')->latest()->get();
        $canManage = auth()->user()->isKoordinatorIt();
        $canGiveFeedback = auth()->user()->isHeadOfStore2();

        return view('it.project', compact('projects', 'canManage', 'canGiveFeedback'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt(), 403);

        $request->validate([
            'nama' => 'required|string|max:255',
            'deadline' => 'required|date',
            'status' => 'required|in:menunggu,proses,selesai',
        ]);

        ItProject::create([
            'nama' => $request->nama,
            'deadline' => $request->deadline,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Project berhasil ditambahkan.');
    }

    public function update(Request $request, ItProject $project)
    {
        abort_unless(auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt(), 403);

        $request->validate([
            'nama' => 'required|string|max:255',
            'deadline' => 'required|date',
            'status' => 'required|in:menunggu,proses,selesai',
        ]);

        $project->update($request->only(['nama', 'deadline', 'status']));

        return back()->with('success', 'Project berhasil diupdate.');
    }

    public function destroy(ItProject $project)
    {
        abort_unless(auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt(), 403);

        $project->delete();
        return back()->with('success', 'Project berhasil dihapus.');
    }

    public function feedback(Request $request, ItProject $project)
    {
        abort_unless($request->user()->isHeadOfStore2(), 403);

        $request->validate([
            'feedback_atasan' => 'required|string|max:3000',
        ]);

        $project->update(['feedback_atasan' => $request->input('feedback_atasan')]);

        return back()->with('success', 'Feedback untuk project "' . $project->nama . '" berhasil disimpan.');
    }
}
