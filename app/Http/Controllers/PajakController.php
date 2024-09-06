<?php

namespace App\Http\Controllers;

use App\Models\InvoiceTagihan;
use App\Models\KasProject;
use Illuminate\Http\Request;

class PajakController extends Controller
{
    public function index()
    {
        $nt = InvoiceTagihan::where('cutoff', 0)->where('finished', 0)->count();
        $it = InvoiceTagihan::where('cutoff', 1)->where('finished', 0)->count();
        $pph = InvoiceTagihan::where('finished', 1)->where('pph_badan', 0)->count();

        $ip = InvoiceTagihan::where('cutoff', 1)
                            ->where('ppn', 0)
                            ->where('finished', 1)
                            ->where('nilai_ppn', '>', 0)
                            ->count();
        $np = KasProject::where('ppn_masuk', 1)->count();
        return view('pajak.index', [
            'nt' => $nt,
            'it' => $it,
            'ip' => $ip,
            'pph' => $pph,
            'np' => $np,
        ]);
    }
}
