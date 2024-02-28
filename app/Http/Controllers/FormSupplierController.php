<?php

namespace App\Http\Controllers;

use App\Models\KasSupplier;
use App\Models\Supplier;
use App\Models\Rekening;
use App\Models\KasBesar;
use App\Models\GroupWa;
use App\Models\PesanWa;
use App\Services\StarSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormSupplierController extends Controller
{

    public function getRekSupplier($id)
    {
        $supplier = Supplier::find($id);

        $data = [
            'nama_rek' => $supplier->nama_rek,
            'bank' => $supplier->bank,
            'no_rek' => $supplier->no_rek,
        ];

        return response()->json($data);
    }

    public function titipan()
    {
        $data = Supplier::select('id', 'nama', 'nickname')->get();

        if($data->count() == 0) {
            return redirect()->back()->with('error', 'Data Supplier Kosong');
        }

        $kas = new KasBesar();
        $nomor = $kas->nomorTitipan();

        return view('billing.form-supplier.titipan', [
            'data' => $data,
            'nomor' => $nomor,
        ]);
    }

    public function titipan_store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'nominal_transaksi' => 'required',
        ]);

        $supplier = Supplier::find($data['supplier_id']);

        $data['nominal_transaksi'] = str_replace('.', '', $data['nominal_transaksi']);

        $kasBesar = new KasBesar();
        $lastKasBesar = $kasBesar->lastKasBesar();

        if ($lastKasBesar == null || $lastKasBesar->saldo < $data['nominal_transaksi']) {
            return redirect()->back()->with('error', 'Saldo Kas Besar Tidak Mencukupi');
        }

        $kas['jenis'] = 0;
        $kas['tanggal'] = date('Y-m-d');
        $kas['uraian'] = 'Titipan Supplier '.$supplier->nickname;
        $kas['nomor_titipan'] = $kasBesar->nomorTitipan();
        $kas['no_rek'] = $supplier->no_rek;
        $kas['nama_rek'] = $supplier->nama_rek;
        $kas['bank'] = $supplier->bank;
        $kas['nominal_transaksi'] = $data['nominal_transaksi'];
        $kas['saldo'] = $lastKasBesar->saldo - $data['nominal_transaksi'];
        $kas['modal_investor_terakhir'] = $lastKasBesar->modal_investor_terakhir;

        $kasSupplier = new KasSupplier();

        $lastSupplier = $kasSupplier->lastKasSupplier($data['supplier_id']);

        $data['jenis'] = 1;
        $data['tanggal'] = date('Y-m-d');
        $data['uraian'] = 'Titipan';

        if ($lastSupplier) {
            $data['saldo'] = $lastSupplier->saldo + $data['nominal_transaksi'];
        } else {
            $data['saldo'] = $data['nominal_transaksi'];
        }

        DB::beginTransaction();

        $store = $kasBesar->create($kas);
        $store2 = $kasSupplier->create($data);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        $pesan =    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                    "*Form Titipan Supplier*\n".
                    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                    "*T".str_pad($store->nomor_titipan, 2, '0', STR_PAD_LEFT)."*\n\n".
                    "Supplier : *".$supplier->nama."*\n\n".
                    "Nilai :  *Rp. ".number_format($data['nominal_transaksi'], 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$kas['bank']."\n".
                    "Nama    : ".$kas['nama_rek']."\n".
                    "No. Rek : ".$kas['no_rek']."\n\n".
                    "==========================\n".
                    "Sisa Kas Supplier : \n".
                    "Rp. ".number_format($store2->saldo, 0, ',', '.')."\n\n".
                    "Sisa Saldo Kas Besar : \n".
                    "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
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

        return redirect()->route('billing')->with('success', 'Data Berhasil Disimpan');

    }

    public function pengembalian()
    {
        $rekening = Rekening::where('untuk', 'kas-besar')->first();
        $supplier = Supplier::select('id', 'nama', 'nickname')->get();

        return view('billing.form-supplier.pengembalian', [
            'rekening' => $rekening,
            'supplier' => $supplier,
        ]);
    }

    public function pengembalian_store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'nominal_transaksi' => 'required',
        ]);

        $data['nominal_transaksi'] = str_replace('.', '', $data['nominal_transaksi']);

        $kasSupplier = new KasSupplier();
        $kasBesar = new KasBesar();
        $supplier = Supplier::find($data['supplier_id']);

        $lastKasSupplier = $kasSupplier->lastKasSupplier($data['supplier_id']);

        if ($lastKasSupplier == null || $lastKasSupplier->saldo < $data['nominal_transaksi']) {
            return redirect()->back()->with('error', 'Saldo Kas Supplier Tidak Mencukupi');
        }
        $data['uraian'] = 'Pengembalian Titipan';

        $k['nominal_transaksi'] = $data['nominal_transaksi'];
        $k['uraian'] = 'Pengembalian Titipan '.$supplier->nickname;

        DB::beginTransaction();

        $s = $kasSupplier->insertKeluar($data);
        $store = $kasBesar->insertMasuk($k);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        $pesan =    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                    "*Form Pengembalian Titipan*\n".
                    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                    "Supplier : ".$s->supplier->nama."\n\n".
                    "Nilai :  *Rp. ".number_format($store->nominal_transaksi, 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$store->bank."\n".
                    "Nama    : ".$store->nama_rek."\n".
                    "No. Rek : ".$store->no_rek."\n\n".
                    "==========================\n".
                    "Sisa Kas Supplier : \n".
                    "Rp. ".number_format($s->saldo, 0, ',', '.')."\n\n".
                    "Sisa Saldo Kas Besar : \n".
                    "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
                    "Total Modal Investor : \n".
                    "Rp. ".number_format($store->modal_investor_terakhir, 0, ',', '.')."\n\n".
                    "Terima kasih 🙏🙏🙏\n";

        $send = new StarSender($group->nama_group, $pesan);
        $res = $send->sendGroup();

        DB::commit();

        return redirect()->route('billing')->with('success', 'Data Berhasil Disimpan');
    }
}
