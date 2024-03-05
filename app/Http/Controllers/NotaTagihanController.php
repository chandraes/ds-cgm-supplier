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
use App\Models\PesanWa;
use App\Services\StarSender;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotaTagihanController extends Controller
{
    public function index()
    {
        $data = InvoiceTagihan::with(['customer', 'project'])->where('finished', 0)->get();

        return view('billing.nota-tagihan.index', [
            'data' => $data,
        ]);
    }

    public function edit_store( Request $request)
    {

    }

    public function cicilan_tagihan(InvoiceTagihan $invoice, Request $request)
    {
        $data = $request->validate([
            'nominal' => 'required',
        ]);
    }

    public function cutoff(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'total_tagih' => 'required|integer',
            'selectedData' => 'required',
        ]);

        // convert selectedData to array and remove empty value
        $selectedData = array_filter(explode(',', $data['selectedData']));

        $db = new InvoiceTagihan;

        $d['tanggal'] = date('Y-m-d');
        $d['customer_id'] = $data['customer_id'];
        $d['total_tagihan'] = $data['total_tagih'];
        $d['no_invoice'] = $db->noInvoice();

        $k['uraian'] = 'Tagihan '. Customer::find($data['customer_id'])->singkatan;
        $k['nominal_transaksi'] = $d['total_tagihan'];
        $k['nomor_tagihan'] = $d['no_invoice'];



        DB::beginTransaction();

        $store = $db->create($d);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        $pesan =    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                    "*Form Tagihan Customer*\n".
                    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                    "*TC".$store->nomor_tagihan."*\n\n".
                    "Customer : ".$invoice->customer->nama."\n\n".
                    "Nilai :  *Rp. ".number_format($store->nominal_transaksi, 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$store->bank."\n".
                    "Nama    : ".$store->nama_rek."\n".
                    "No. Rek : ".$store->no_rek."\n\n".
                    "==========================\n".
                    $pesan2.
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

        // dd($res);

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

        return redirect()->route('billing')->with('success', 'Berhasil menyimpan data tagihan.');

    }
}
