<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\KasProject;
use App\Models\Project;
use App\Services\StarSender;
use App\Models\GroupWa;
use App\Models\KasBesar;
use App\Models\PesanWa;
use App\Models\Rekening;
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
        $kb = new KasBesar();

        $data['nominal'] = str_replace('.', '', $data['nominal']);

        $saldo = $kb->saldoTerakhir();


        if ($saldo < $data['nominal']) {
            return redirect()->back()->with('error', 'Saldo Kas Besar tidak mencukupi. Saldo Kas Besar terakhir: Rp. '.number_format($saldo, 0, ',', '.'));
        }

        $store = $db->transaksiKeluar($data);

        $sisa = $db->sisaTerakhir($store->project_id);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        $pesan =    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                    "*Form Transaksi (Dana Keluar)*\n".
                    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                    "Project : "."*".$store->project->nama."*\n\n".
                    "Uraian :  *".$store->uraian."*\n".
                    "Nilai    :  *Rp. ".number_format($store->nominal, 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$store->bank."\n".
                    "Nama    : ".$store->nama_rek."\n".
                    "No. Rek : ".$store->no_rek."\n\n".
                    "==========================\n".
                    "Sisa Saldo Kas Besar : \n".
                    "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
                    "Total Modal Investor : \n".
                    "Rp. ".number_format($store->modal_investor_terakhir, 0, ',', '.')."\n\n".
                    "Total Kas Project : \n".
                    "Rp. ".number_format($sisa, 0, ',', '.')."\n\n".
                    "Terima kasih 🙏🙏🙏\n";

        //Tambahkan total pengeluaran project

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
        $rekening = Rekening::where('untuk', 'kas-besar')->first();
        return view('billing.form-transaksi.masuk', [
            'project' => $project,
            'rekening' => $rekening,
        ]);
    }

    public function masuk_store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nominal' => 'required',
            'uraian' => 'required',
        ]);

        session(['project_id' => $data['project_id']]);

        $db = new KasProject();

        $store = $db->transaksiMasuk($data);

        $sisa = $db->sisaTerakhir($store->project_id);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        $pesan =    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                    "*Form Transaksi (Dana Masuk)*\n".
                    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                    $store->uraian."\n\n".
                    "Project :  *".$store->project->nama."*\n\n".
                    "Nilai    :  *Rp. ".number_format($store->nominal, 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$store->bank."\n".
                    "Nama    : ".$store->nama_rek."\n".
                    "No. Rek : ".$store->no_rek."\n\n".
                    "==========================\n".
                    "Sisa Saldo Kas Besar : \n".
                    "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
                    "Total Modal Investor : \n".
                    "Rp. ".number_format($store->modal_investor_terakhir, 0, ',', '.')."\n\n".
                    "Total Kas Project : \n".
                    "Rp. ".number_format($sisa, 0, ',', '.')."\n\n".
                    "Terima kasih 🙏🙏🙏\n";

        //Tambahkan total pengeluaran project

        $send = new StarSender($group->nama_group, $pesan);
        $res = $send->sendGroup();

        $status = ($res == 'true') ? 1 : 0;

        PesanWa::create([
            'pesan' => $pesan,
            'tujuan' => $group->nama_group,
            'status' => $status,
        ]);

        return redirect()->route('billing')->with('success', 'Transaksi berhasil ditambahkan');

    }

}
