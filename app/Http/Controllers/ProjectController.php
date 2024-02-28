<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request)
    {

        return view('db.project.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customer,id',
            'nama' => 'required',
            'nilai' => 'required',
            'tanggal_mulai' => 'required',
            'jatuh_tempo' => 'required',
            'project_status_id' => 'required|exists:project_statuses,id',
        ]);

        DB::beginTransaction();

        try {
            Project::createProject($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('projects.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project berhasil dibuat!');
    }
}
