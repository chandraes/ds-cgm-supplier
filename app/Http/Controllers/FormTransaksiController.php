<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\KasProject;
use App\Models\Project;
use App\Services\StarSender;
use App\Models\GroupWa;
use App\Models\PesanWa;
use Illuminate\Http\Request;

class FormTransaksiController extends Controller
{
    public function index()
    {
        $project = Project::where('project_status_id', 1)->get();
        return view('billing.form-transaksi.keluar', [
            'project' => $project,
        ]);
    }
    public function tambah()
    {
        $project = Project::all();

        return view('billing.form-transaksi.index', [
            'project' => $project,
        ]);
    }

    public function tambah_store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nominal' => 'required',
            'uraian' => 'required',
            'no_rek' => 'required',
            'nama_rek' => 'required',
            'bank' => 'required',
        ]);

        session(['project_id' => $data['project_id'],
                'nama_rek' => $data['nama_rek'],
                'no_rek' => $data['no_rek'],
                'bank' => $data['bank']]);

        $db = new KasProject();

        $data['nominal'] = str_replace('.', '', $data['nominal']);

        $saldo = $db->saldo_project_terakhir($data['project_id']);

        if ($saldo < $data['nominal']) {
            return redirect()->back()->with('error', 'Saldo Kas Project tidak mencukupi. Saldo Kas Project terakhir: Rp. '.number_format($saldo, 0, ',', '.'));
        }

        $store = $db->tambahTransaksi($data);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        $pesan =    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                    "*Form Pengeluaran Project*\n".
                    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                    "*".$store->project->nama."*\n\n".
                    "Uraian :  *".$store->uraian."*\n".
                    "Nilai   :  *Rp. ".number_format($store->nominal, 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$store->bank."\n".
                    "Nama    : ".$store->nama_rek."\n".
                    "No. Rek : ".$store->no_rek."\n\n".
                    "==========================\n".
                    "Terima kasih 🙏🙏🙏\n";

        $send = new StarSender($group->nama_group, $pesan);
        $res = $send->sendGroup();

        $status = ($res == 'true') ? 1 : 0;

        PesanWa::create([
            'pesan' => $pesan,
            'tujuan' => $group->nama_group,
            'status' => $status,
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan');

    }

    public function masuk()
    {
        $project = Project::where('project_status_id', 1)->get();
        return view('billing.form-transaksi.masuk', [
            'project' => $project,
        ]);
    }

    public function masuk_store(Request $request)
    {

    }

}
