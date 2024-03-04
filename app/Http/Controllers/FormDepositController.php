<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use App\Models\Transaksi;
use App\Models\InvoicePpn;
use App\Services\StarSender;
use App\Models\PesanWa;
use App\Models\GroupWa;
use App\Models\KasProject;
use Illuminate\Http\Request;

class FormDepositController extends Controller
{
    public function masuk()
    {
        $db = new KasProject();

        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        return view('billing.form-deposit.masuk', [
            'rekening' => $rekening,
        ]);
    }

    public function masuk_store(Request $request)
    {
        $data = $request->validate([
            'nominal_transaksi' => 'required',
        ]);

        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        $data['tanggal'] = date('Y-m-d');
        $data['jenis'] = 1;
        $data['nominal_transaksi'] = str_replace('.', '', $data['nominal_transaksi']);
        $data['modal_investor'] = -$data['nominal_transaksi'];

        $kasBesar = new KasBesar();
        $transaksi = new Transaksi;
        $ppn = new InvoicePpn;
        $lastKasBesar = $kasBesar->lastKasBesar();

        if ($lastKasBesar) {
            $data['saldo'] = $lastKasBesar->saldo + $data['nominal_transaksi'];
            $data['modal_investor_terakhir'] = $lastKasBesar->modal_investor_terakhir - $data['nominal_transaksi'];
        } else {
            $data['saldo'] = $data['nominal_transaksi'];
            $data['modal_investor_terakhir'] = $data['modal_investor'];
        }
        $data['nomor_deposit'] = $kasBesar->nomorDeposit();
        $data['no_rek'] = $rekening->no_rek;
        $data['nama_rek'] = $rekening->nama_rek;
        $data['bank'] = $rekening->bank;

        $store = KasBesar::create($data);

        $totalPpn = $ppn->where('bayar', 0)->sum('total_ppn');
        $last = $kasBesar->lastKasBesar()->saldo ?? 0;
        $modalInvestor = ($kasBesar->lastKasBesar()->modal_investor_terakhir ?? 0) * -1;
        $totalTagihan = $transaksi->totalTagihan()->sum('total_tagihan');

        $total_profit_bulan = ($totalTagihan+$last)-($modalInvestor+$totalPpn);

        $group = GroupWa::where('untuk', 'kas-besar')->first();
        $pesan =    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                    "*Form Permintaan Deposit*\n".
                    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                    "*D".$store->nomor_deposit."*\n\n".
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

        if ($res == 'true') {
            PesanWa::create([
                'pesan' => $pesan,
                'tujuan' => $group->nama_group,
                'status' => 1,
            ]);
        } else {
            PesanWa::create([
                'pesan' => $pesan,
                'tujuan' => $group->nama_group,
                'status' => 0,
            ]);
        }

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
