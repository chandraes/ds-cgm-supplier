<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Transaksi;
use App\Models\InvoicePpn;
use App\Models\InvoiceTagihan;
use App\Models\InvoiceTagihanDetail;
use App\Models\KasBesar;
use App\Models\GroupWa;
use App\Models\Investor;
use App\Models\PesanWa;
use App\Services\StarSender;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotaTagihanController extends Controller
{
    public function index()
    {
        $data = InvoiceTagihan::with(['customer', 'project','kasProjects', 'invoiceTagihanDetails'])->where('finished', 0)->get();

        return view('billing.nota-tagihan.index', [
            'data' => $data,
        ]);
    }

    public function cicilan(InvoiceTagihan $invoice, Request $request)
    {
        $data = $request->validate([
            'nominal' => 'required',
            'uraian' => 'required',
        ]);

        $db = new InvoiceTagihan();

        $store = $db->cicilan($invoice->id, $data);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        $pesan =    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                    "*Form Tagihan*\n".
                    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                    "Project : *".$store->project->nama."*\n".
                    "Uraian : *".$store->uraian."*\n\n".
                    "Nilai   :  *Rp. ".number_format($store->nominal, 0, ',', '.')."*\n\n".
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

        //Tambahkan sisa tagihan

        $send = new StarSender($group->nama_group, $pesan);
        $res = $send->sendGroup();

        $status = ($res == 'true') ? 1 : 0;

        PesanWa::create([
            'pesan' => $pesan,
            'tujuan' => $group->nama_group,
            'status' => $status,
        ]);

        return redirect()->back()->with('success', 'Cicilan berhasil ditambahkan');


    }

    public function pelunasan(InvoiceTagihan $invoice)
    {
        ini_set('max_execution_time', 180);
        ini_set('memory_limit', '32M');


        $kb = new KasBesar();
        $db = new InvoiceTagihan();

        $saldo = $kb->saldoTerakhir() + $invoice->sisa_tagihan;
        $pengeluaran = ($invoice->kasProjects()->orderBy('id', 'desc')->first()->sisa * -1) + ($invoice->profit > 0 ? $invoice->profit : 0);

        if ($saldo < $pengeluaran) {
            return redirect()->back()->with('error', 'Saldo Kas Besar tidak mencukupi untuk proses pelunasan!');
        }

        $check = Investor::sum('persentase');

        if ($check < 100) {
            return redirect()->back()->with('error', 'Total persentase investor belum mencapai 100%');
        }

        $save = $db->pelunasan($invoice->id);

        return redirect()->back()->with(($save['status'] == 0 ? "error" : "success"), $save['message']);
    }
}
