<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use App\Services\StarSender;
use App\Models\PesanWa;
use App\Models\GroupWa;
use App\Models\KasBesar;
use App\Models\KasProject;
use App\Models\Project;
use Illuminate\Http\Request;

class FormDepositController extends Controller
{
    public function masuk()
    {

        $rekening = Rekening::where('untuk', 'kas-besar')->first();
        $kode = str_pad((KasBesar::max('nomor_deposit') + 1), 2, '0', STR_PAD_LEFT);

        return view('billing.form-deposit.masuk', [
            'rekening' => $rekening,
            'kode' => $kode
        ]);
    }

    public function masuk_store(Request $request)
    {
        $data = $request->validate([
            'nominal' => 'required',
        ]);

        $db = new KasBesar();

        $store = $db->deposit($data);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        $pesan =    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                    "*Form Permintaan Deposit*\n".
                    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                    "*".$store->kode_deposit."*\n\n".
                    "Nilai :  *Rp. ".number_format($store->nominal, 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$store->bank."\n".
                    "Nama    : ".$store->nama_rek."\n".
                    "No. Rek : ".$store->no_rek."\n\n".
                    "==========================\n".
                    "Sisa Saldo Kas Besar : \n".
                    "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
                    "Total Modal Investor : \n".
                    "Rp. ".number_format($store->modal_investor_terakhir, 0, ',', '.')."\n\n".
                    "Terima kasih 🙏🙏🙏\n";
        $send = new StarSender($group->nama_group, $pesan);
        $res = $send->sendGroup();

        $status = ($res == 'true') ? 1 : 0;

        PesanWa::create([
            'pesan' => $pesan,
            'tujuan' => $group->nama_group,
            'status' => $status,
        ]);

        return redirect()->route('billing')->with('success', 'Berhasil menambahkan data');
    }

    public function keluar()
    {
        $rekening = Rekening::where('untuk', 'withdraw')->first();

        return view('billing.form-deposit.keluar', [
            'rekening' => $rekening,
        ]);
    }

    public function getModalInvestorProject(Request $request)
    {
        $db = new KasProject;
        $result = $db->modal_investor_project_terakhir($request->project_id) * -1;
        $result = number_format($result, 0, ',', '.');

        return response()->json($result);
    }

    public function keluar_store(Request $request)
    {
        $data = $request->validate([
            'nominal' => 'required',
        ]);

        $db = new KasBesar();
        $modal = $db->modalInvestorTerakhir() * -1;
        $saldo = $db->saldoTerakhir();

        $data['nominal'] = str_replace('.', '', $data['nominal']);

        if($modal < $data['nominal'] || $saldo < $data['nominal']){
            return redirect()->back()->with('error', 'Nominal Melebihi Modal Investor/Saldo !!');
        }

        $store = $db->withdraw($data);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        $pesan =    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                    "*Form Pengembalian Deposit*\n".
                    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                    "Nilai :  *Rp. ".number_format($store->nominal, 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$store->bank."\n".
                    "Nama    : ".$store->nama_rek."\n".
                    "No. Rek : ".$store->no_rek."\n\n".
                    "==========================\n".
                    "Sisa Saldo Kas Besar : \n".
                    "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
                    "Total Modal Investor : \n".
                    "Rp. ".number_format($store->modal_investor_terakhir, 0, ',', '.')."\n\n".
                    "Terima kasih 🙏🙏🙏\n";

        $send = new StarSender($group->nama_group, $pesan);
        $res = $send->sendGroup();

        $status = ($res == 'true') ? 1 : 0;

        PesanWa::create([
            'pesan' => $pesan,
            'tujuan' => $group->nama_group,
            'status' => $status,
        ]);

        return redirect()->route('billing')->with('success', 'Data berhasil disimpan');
    }
}
