<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use App\Models\KasBesar;
use App\Models\KasSupplier;
use App\Models\Transaksi;
use App\Models\InvoicePpn;
use App\Models\GroupWa;
use App\Models\PesanWa;
use App\Services\StarSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormLainController extends Controller
{
    public function masuk()
    {
        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        return view('billing.lain-lain.masuk', [
            'rekening' => $rekening
        ]);
    }

    public function masuk_store(Request $request)
    {
        $data = $request->validate([
            'uraian' => 'required',
            'nominal_transaksi' => 'required',
        ]);

        $data['tanggal'] = date('Y-m-d');
        $data['nominal_transaksi'] = str_replace('.', '', $data['nominal_transaksi']);

        $kas = new KasBesar;
        $ppn = new InvoicePpn;
        $transaksi = new Transaksi;
        $kasSupplier = new KasSupplier;
        $totalPpn = $ppn->where('bayar', 0)->sum('total_ppn');

        $rekening = Rekening::where('untuk', 'kas-besar')->first();
        $lastKasBesar = $kas->lastKasBesar();


        if ($lastKasBesar == null || $lastKasBesar->saldo < $data['nominal_transaksi']) {
            return redirect()->back()->with('error', 'Saldo Kas Besar Tidak Cukup!!');
        }
        $data['modal_investor_terakhir'] = $lastKasBesar->modal_investor_terakhir;
        $data['saldo'] = $lastKasBesar->saldo + $data['nominal_transaksi'];
        $data['jenis'] = 1;
        $data['no_rek'] = $rekening->no_rek;
        $data['nama_rek'] = $rekening->nama_rek;
        $data['bank'] = $rekening->bank;

        DB::beginTransaction();

        $store = KasBesar::create($data);

        $last = $kas->lastKasBesar()->saldo ?? 0;
        $modalInvestor = ($kas->lastKasBesar()->modal_investor_terakhir ?? 0) * -1;
        $totalTagihan = $transaksi->totalTagihan()->sum('total_tagihan');
        $totalTitipan = $kasSupplier->saldoTitipan() ?? 0;

        $total_profit_bulan = ($totalTitipan+$totalTagihan+$last)-($modalInvestor+$totalPpn);

        $group = GroupWa::where('untuk', 'kas-besar')->first();
        $pesan ="🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                "*Form Lain2 (Dana Masuk)*\n".
                "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                "Uraian :  ".$data['uraian']."\n".
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

        DB::commit();

        return redirect()->route('billing')->with('success', 'Data Berhasil Ditambahkan');

    }

    public function keluar()
    {
        return view('billing.lain-lain.keluar');
    }

    public function keluar_store(Request $request)
    {
        $data = $request->validate([
            'uraian' => 'required',
            'nominal_transaksi' => 'required',
            'nama_rek' => 'required',
            'no_rek' => 'required',
            'bank' => 'required',
        ]);

        $data['nominal_transaksi'] = str_replace('.', '', $data['nominal_transaksi']);
        $kas = new KasBesar;
        $ppn = new InvoicePpn;
        $transaksi = new Transaksi;
        $kasSupplier = new KasSupplier;

        $totalPpn = $ppn->where('bayar', 0)->sum('total_ppn');

        $lastKasBesar = $kas->lastKasBesar();

        if ($lastKasBesar == null || $lastKasBesar->saldo < $data['nominal_transaksi']) {
            return redirect()->back()->with('error', 'Saldo Kas Besar Tidak Cukup!!');
        }

        DB::beginTransaction();

        $store = $kas->lainKeluar($data);

        $last = $kas->lastKasBesar()->saldo ?? 0;
        $modalInvestor = ($kas->lastKasBesar()->modal_investor_terakhir ?? 0) * -1;
        $totalTagihan = $transaksi->totalTagihan()->sum('total_tagihan');
        $totalTitipan = $kasSupplier->saldoTitipan() ?? 0;

        $total_profit_bulan = ($totalTitipan+$totalTagihan+$last)-($modalInvestor+$totalPpn);

        $group = GroupWa::where('untuk', 'kas-besar')->first();
        $pesan ="🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                "*Form Lain2 (Dana Keluar)*\n".
                 "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                 "Uraian :  ".$data['uraian']."\n".
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

        DB::commit();

        return redirect()->route('billing')->with('success', 'Data Berhasil Ditambahkan');

    }
}
