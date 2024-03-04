<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class FormTransaksiController extends Controller
{
    public function tambah(Customer $customer)
    {


        $project = Project::all();

        return view('billing.form-transaksi.index', [
            'customer' => $customer,
            'project' => $project,
        ]);
    }

    public function tambah_store(Request $request)
    {


    }

    public function edit_store(Request $request, Transaksi $transaksi)
    {

    }

    public function delete(Transaksi $transaksi)
    {

    }

    public function lanjutkan(Customer $customer)
    {

    }
}
