<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $data = Customer::all();
        return view('db.customer.index', [
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required',
            'npwp' => 'required',
            'singkatan' => 'required',
            'cp' => 'required',
            'no_wa' => 'required',
            'alamat' => 'required',
        ]);

        DB::beginTransaction();
        try {
            Customer::create($data);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with('success', 'Data berhasil ditambahkan!');

    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'nama' => 'required',
            'npwp' => 'required',
            'singkatan' => 'required',
            'cp' => 'required',
            'no_wa' => 'required',
            'alamat' => 'required',
            'harga' => 'required',
        ]);

        $data['harga'] = str_replace('.', '', $data['harga']);

        $customer->update($data);

        return redirect()->back()->with('success', 'Data berhasil diubah!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }

    public function update_harga(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'harga' => 'required',
        ]);

        $data['harga'] = str_replace('.', '', $data['harga']);

        $customer->update($data);

        return redirect()->back()->with('success', 'Data berhasil diubah!');
    }
}
