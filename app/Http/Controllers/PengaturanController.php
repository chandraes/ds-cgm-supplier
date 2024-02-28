<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
Use App\Http\Controllers\Hash;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $users = User::all();
        $suppliers = Supplier::all();
        return view('pengaturan.pengguna.index', [
            'data' => $users,
            'suppliers' => $suppliers,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pengaturan.pengguna.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'name' => 'required|string|max:255',
            'email' => 'nullable',
            'password' => 'required',
            'role' => 'required',
            'supplier_id' => 'nullable',
        ]);

        $data['password'] = bcrypt($data['password']);

        User::create($data);

        return redirect()->route('pengaturan.akun')->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        // dd($user);
        return view('pengaturan.pengguna.edit',  compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'nullable',
            'password' => 'nullable',
            'role' => 'required',
            'supplier_id' => 'nullable',
        ]);

        $user = User::findOrFail($id);

        if ($request->password) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if ($data['role'] != 'supplier') {
            $data['supplier_id'] = null;
        }

        try {
            $user->update($data);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Username sudah digunakan!');
        }

        return redirect()->route('pengaturan.akun')->with('success', 'Data berhasil diubah!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $check = User::count();

        if ($check == 1) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus user, karena hanya ada 1 user');
        }

        DB::transaction(function () use ($id) {
            $user = User::findOrFail($id);

            // Pengecekan apakah hanya tersisa satu data
            $totalUsers = User::count();
            if ($totalUsers > 1) {
                $user->delete();
            } else {
                return redirect()->route('pengaturan.akun')->with('error', 'Tidak dapat menghapus satu-satunya pengguna.');
            }
        });

        return redirect()->route('pengaturan.akun')->with('success', 'User has been deleted');
    }

}
