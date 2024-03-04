<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $customer = Customer::all();
        $data = Project::with(['customer', 'project_status'])->whereNot('project_status_id', 3)->get();

        return view('db.project.index',
            [
                'customers' => $customer,
                'data' => $data
            ]
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'nama' => 'required',
            'nomor_kontrak' => 'required',
            'nilai' => 'required',
            'tanggal_mulai' => 'required',
            'jatuh_tempo' => 'required',
            // 'project_status_id' => 'required|exists:project_statuses,id',
        ]);

        $data['project_status_id'] = 1; // status project default [1 = On Progress

        DB::beginTransaction();

        try {
            $store = Project::createProject($data);

            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('db.project')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('db.project')
            ->with('success', 'Project berhasil dibuat!');
    }

    public function update(Project $project, Request $request)
    {
        // dd($request->all()); // check if the project is exist or not (debugging purpose
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'nama' => 'required',
            'nilai' => 'required',
            'nomor_kontrak' => 'required',
            'tanggal_mulai' => 'required',
            'jatuh_tempo' => 'required',
        ]);


        DB::beginTransaction();

        try {
            Project::updateProject($project->id, $data);
            DB::commit();

        return redirect()->route('db.project')
            ->with('success', 'Project berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('db.project')
                ->with('error', $e->getMessage());
        }


    }
}
