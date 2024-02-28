<?php

namespace App\Http\Controllers;

use App\Models\KasBesar;
use App\Models\Investor;
use App\Models\Transaksi;
use App\Models\InvoicePpn;
use App\Models\KasSupplier;
use App\Models\Rekening;
use App\Models\GroupWa;
use App\Models\PesanWa;
use Illuminate\Http\Request;
use App\Services\StarSender;
use Carbon\Carbon;

class FormDevidenController extends Controller
{
    public function index()
    {
        $transaksi = new Transaksi();
        $db = new KasBesar();
        $kasSupplier = new KasSupplier();

        $kasBesar = $db->lastKasBesar()->saldo ?? 0;
        $modalInvestor = ($db->lastKasBesar()->modal_investor_terakhir ?? 0) * -1;
        $totalTagihan = $transaksi->totalTagihan()->sum('total_tagihan');
        $totalTitipan = $kasSupplier->saldoTitipan() ?? 0;

        $ppn = InvoicePpn::where('bayar', false)->sum('total_ppn');

        $investors = Investor::all();
        $totalPersentase = $investors->sum('persentase');

        if ($totalPersentase != 100) {
            return redirect()->route('billing')->with('error', 'Total persentase investor belum mencapai 100%');
        }

        return view('billing.deviden.index', [
            'data' => $investors,
            'totalTagihan' => $totalTagihan,
            'totalTitipan' => $totalTitipan,
            'kasBesar' => $kasBesar,
            'modalInvestor' => $modalInvestor,
            'ppn' => $ppn,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nominal_transaksi' => 'required',
        ]);

        $data['nominal_transaksi'] = str_replace('.', '', $data['nominal_transaksi']);


        $last = KasBesar::latest()->first();


        if ($last == null || $last->saldo < $data['nominal_transaksi']) {
            return redirect()->route('billing.deviden.index')->with('error', 'Saldo tidak cukup');
        }

        $investor = Investor::all();
        $group = GroupWa::where('untuk', 'kas-besar')->first();
        $month = Carbon::now()->locale('id')->monthName;

        $isiPesan = [];

        foreach ($investor as $v) {
            usleep(50000);

            $last2 = KasBesar::latest()->orderBy('id', 'desc')->first();
            $nilai2 = $data['nominal_transaksi'] * $v->persentase / 100;
            // $rekening = Rekening::where('untuk', 'withdraw')->first();
            $ppn = new InvoicePpn;
            $db = new KasBesar;
            $transaksi = new Transaksi;
            $kasSupplier = new KasSupplier;


                $k['tanggal'] = date('Y-m-d');
                $k['jenis'] = 0;
                $k['uraian'] = "Bagi Deviden ".$v->nama;
                $k['nominal_transaksi'] = $nilai2;
                $k['saldo'] = $last2->saldo - $nilai2;
                $k['nama_rek'] = substr($v->nama_rek, 0, 15);
                $k['bank'] = $v->bank;
                $k['no_rek'] = $v->no_rek;
                $k['modal_investor_terakhir'] = $last2->modal_investor_terakhir;

            $store = KasBesar::create($k);
            //  dd($store);

            $totalPpn = $ppn->where('bayar', 0)->sum('total_ppn');
            $last = $db->lastKasBesar()->saldo ?? 0;
            $modalInvestor = ($db->lastKasBesar()->modal_investor_terakhir ?? 0) * -1;
            $totalTagihan = $transaksi->totalTagihan()->sum('total_tagihan');
            $totalTitipan = $kasSupplier->saldoTitipan() ?? 0;

            $total_profit_bulan = ($totalTitipan+$totalTagihan+$last)-($modalInvestor+$totalPpn);

            $pesan = "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                    "*Form Deviden ".$month."*\n".
                    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                    "Nama  : ".$v->nama."\n".
                    "Nilai :  *Rp. ".number_format($k['nominal_transaksi'], 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$k['bank']."\n".
                    "Nama    : ".$k['nama_rek']."\n".
                    "No. Rek : ".$k['no_rek']."\n\n".
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

            array_push($isiPesan, $pesan);
        }

        // looping $isiPesan
        foreach ($isiPesan as $pesan) {
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

        }

        return redirect()->route('billing')->with('success', 'Data berhasil disimpan');
    }

}
