<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use App\Services\StarSender;
use App\Models\PesanWa;
use App\Models\GroupWa;
use App\Models\KasProject;
use App\Models\Project;
use Illuminate\Http\Request;

class FormDepositController extends Controller
{
    public function masuk()
    {
        $project = Project::where('project_status_id', 1)->get();

        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        return view('billing.form-deposit.masuk', [
            'rekening' => $rekening,
            'projects' => $project
        ]);
    }

    public function masuk_store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nominal' => 'required',
        ]);

        $db = new KasProject;

        $store = $db->masukDeposit($data);

        $group = GroupWa::where('untuk', 'kas-besar')->first();
        $pesan =    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                    "*Form Permintaan Deposit*\n".
                    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                    "*".$store->project->nama."*\n\n".
                    "Nilai :  *Rp. ".number_format($store->nominal, 0, ',', '.')."*\n\n".
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

        return redirect()->route('billing')->with('success', 'Berhasil menambahkan data');
    }

    public function keluar()
    {
        $rekening = Rekening::where('untuk', 'withdraw')->first();

        return view('billing.form-deposit.keluar', [
            'rekening' => $rekening
        ]);
    }

    public function keluar_store(Request $request)
    {
        $data = $request->validate([
            'nominal_transaksi' => 'required',
        ]);

        $rekening = Rekening::where('untuk', 'withdraw')->first();

        $data['uraian'] = 'Withdraw';
        $data['nominal_transaksi'] = str_replace('.', '', $data['nominal_transaksi']);
        $data['jenis'] = 0;
        $data['tanggal'] = date('Y-m-d');
        $data['nama_rek'] = substr($rekening->nama_rek, 0, 15);
        $data['no_rek'] = $rekening->no_rek;
        $data['bank'] = $rekening->bank;

        $kasBesar = new KasBesar;
        $transaksi = new Transaksi;
        $ppn = new InvoicePpn;
        $last = $kasBesar->lastKasBesar();

        if($last == null || $last->saldo < $data['nominal_transaksi']){

            return redirect()->back()->with('error', 'Saldo Kas Besar Tidak Cukup');

        }else{
            $data['saldo'] = $last->saldo - $data['nominal_transaksi'];
            $data['modal_investor'] = $data['nominal_transaksi'];
            $data['modal_investor_terakhir']= $last->modal_investor_terakhir + $data['nominal_transaksi'];
        }

        $store = KasBesar::create($data);

        $totalPpn = $ppn->where('bayar', 0)->sum('total_ppn');
        $last = $kasBesar->lastKasBesar()->saldo ?? 0;
        $modalInvestor = ($kasBesar->lastKasBesar()->modal_investor_terakhir ?? 0) * -1;
        $totalTagihan = $transaksi->totalTagihan()->sum('total_tagihan');

        $total_profit_bulan = ($totalTagihan+$last)-($modalInvestor+$totalPpn);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        $pesan =    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                    "*Form Pengembalian Deposit*\n".
                    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                    "Nilai :  *Rp. ".number_format($data['nominal_transaksi'], 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$data['bank']."\n".
                    "Nama    : ".$data['nama_rek']."\n".
                    "No. Rek : ".$data['no_rek']."\n\n".
                    "==========================\n".
                    "Sisa Saldo Kas Besar : \n".
                    "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
                    "Total Profit Saat Ini :" ."\n".
                    "Rp. ".number_format($total_profit_bulan, 0,',','.')."\n\n".
                    "Total PPN Belum Disetor : \n".
                    "Rp. ".number_format($totalPpn, 0, ',', '.')."\n\n".
                    "Total Modal Investor : \n".
                    "Rp. ".number_format($store->modal_investor_terakhir, 0, ',', '.')."\n\n".
                    "Terima kasih 🙏🙏🙏\n";
        $send = new StarSender($group->nama_group, $pesan);
        $res = $send->sendGroup();

        return redirect()->route('billing')->with('success', 'Data berhasil disimpan');
    }
}
