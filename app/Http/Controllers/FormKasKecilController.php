<?php

namespace App\Http\Controllers;

use App\Models\KasBesar;
use App\Models\KasKecil;
use Illuminate\Http\Request;

class FormKasKecilController extends Controller
{
    public function masuk()
    {

    }

    public function masuk_store(Request $request)
    {
        $data = $request->validate([
            'nominal' => 'required',
        ]);

        $db = new KasKecil();

        $store = $db->kasKecil($data);

        return redirect()->route('kas-kecil.index');
    }

    public function keluar()
    {

    }

    public function keluar_store(Request $request)
    {
        $data = $request->validate([
            'nominal' => 'required',
        ]);

        $db = new KasKecil();

        $store = $db->kasKecil($data);

        return redirect()->route('kas-kecil.index');
    }
}
