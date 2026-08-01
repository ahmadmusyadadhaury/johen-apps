<?php

namespace App\Http\Controllers;

use App\Models\ItProject;
use Illuminate\Http\Request;

class ProjectItController extends Controller
{
    public function index()
    {
        $projects = ItProject::with('creator')->latest()->get();
        return view('it.project', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deadline' => 'required|date',
        ]);

        ItProject::create([
            'nama' => $request->nama,
            'deadline' => $request->deadline,
            'status' => 'aktif',
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Project berhasil ditambahkan.');
    }

    public function update(Request $request, ItProject $project)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deadline' => 'required|date',
            'status' => 'required|in:aktif,selesai',
        ]);

        $project->update($request->only(['nama', 'deadline', 'status']));

        return back()->with('success', 'Project berhasil diupdate.');
    }

    public function destroy(ItProject $project)
    {
        $project->delete();
        return back()->with('success', 'Project berhasil dihapus.');
    }
}
