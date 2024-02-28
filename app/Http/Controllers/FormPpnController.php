<?php

namespace App\Http\Controllers;

use App\Models\InvoicePpn;
use App\Models\Transaksi;
use App\Models\KasSupplier;
use App\Models\KasBesar;
use App\Services\StarSender;
use App\Models\GroupWa;
use App\Models\PesanWa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormPpnController extends Controller
{
    public function index()
    {
        $data = InvoicePpn::where('bayar', false)->get();
        return view('billing.form-ppn.index', [
            'data' => $data
        ]);
    }

    public function bayar(Request $request, InvoicePpn $invoice)
    {
        $data = $request->validate([
            'nama_rek' => 'required',
            'no_rek' => 'required',
            'bank' => 'required',
        ]);


        $data['nominal_transaksi'] = $invoice->total_ppn;

        $db = new KasBesar;
        $ppn = new InvoicePpn;
        $transaksi = new Transaksi;
        $kasSupplier = new KasSupplier;

        $saldo = $db->lastKasBesar()->saldo ?? 0;

        $data['uraian'] = "Bayar ".$db->where('invoice_ppn_id', $invoice->id)->first()->uraian;

        if ($saldo < $data['nominal_transaksi']) {
            return redirect()->back()->with('error', 'Saldo kas besar tidak cukup');
        }

        DB::beginTransaction();

        $store = $db->bayarPpn($data);

        $invoice->update([
            'bayar' => true
        ]);

        $totalPpn = $ppn->where('bayar', 0)->sum('total_ppn');
        $last = $db->lastKasBesar()->saldo ?? 0;
        $modalInvestor = ($db->lastKasBesar()->modal_investor_terakhir ?? 0) * -1;
        $totalTagihan = $transaksi->totalTagihan()->sum('total_tagihan');
        $totalTitipan = $kasSupplier->saldoTitipan() ?? 0;

        $total_profit_bulan = ($totalTitipan+$totalTagihan+$last)-($modalInvestor+$totalPpn);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        $pesan =    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                    "*Form PPn Customer*\n".
                    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                    "Uraian : ".$store->uraian."\n\n".
                    "Nilai :  *Rp. ".number_format($store->nominal_transaksi, 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$store->bank."\n".
                    "Nama    : ".$store->nama_rek."\n".
                    "No. Rek : ".$store->no_rek."\n\n".
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

        return redirect()->route('billing')->with('success', 'Berhasil bayar ppn');
    }
}
