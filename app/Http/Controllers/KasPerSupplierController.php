<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\KasSupplier;
use App\Models\InvoiceBayar;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KasPerSupplierController extends Controller
{
    public function index(Request $request)
    {
        $supplier = Supplier::findOrfail(auth()->user()->supplier_id);

        $kas = new KasSupplier;

        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $dataTahun = $kas->dataTahun();

        $data = $kas->kasSupplierNow($supplier->id, $bulan, $tahun);

        $bulanSebelumnya = $bulan - 1;
        $bulanSebelumnya = $bulanSebelumnya == 0 ? 12 : $bulanSebelumnya;
        $tahunSebelumnya = $bulanSebelumnya == 12 ? $tahun - 1 : $tahun;
        $stringBulan = Carbon::createFromDate($tahun, $bulanSebelumnya)->locale('id')->monthName;
        $stringBulanNow = Carbon::createFromDate($tahun, $bulan)->locale('id')->monthName;

        $dataSebelumnya = $kas->lastKasSupplierByMonth($supplier->id,$bulanSebelumnya, $tahunSebelumnya);

        return view('kas-per-supplier.index', [
            'supplier' => $supplier,
            'data' => $data,
            'dataTahun' => $dataTahun,
            'dataSebelumnya' => $dataSebelumnya,
            'stringBulan' => $stringBulan,
            'tahun' => $tahun,
            'tahunSebelumnya' => $tahunSebelumnya,
            'bulan' => $bulan,
            'stringBulanNow' => $stringBulanNow,
        ]);
    }

    public function print(Request $request)
    {
        $supplier = Supplier::findOrfail(auth()->user()->supplier_id);

        $kas = new KasSupplier;

        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $dataTahun = $kas->dataTahun();

        $data = $kas->kasSupplierNow($supplier->id, $bulan, $tahun);

        $bulanSebelumnya = $bulan - 1;
        $bulanSebelumnya = $bulanSebelumnya == 0 ? 12 : $bulanSebelumnya;
        $tahunSebelumnya = $bulanSebelumnya == 12 ? $tahun - 1 : $tahun;
        $stringBulan = Carbon::createFromDate($tahun, $bulanSebelumnya)->locale('id')->monthName;
        $stringBulanNow = Carbon::createFromDate($tahun, $bulan)->locale('id')->monthName;

        $dataSebelumnya = $kas->lastKasSupplierByMonth($supplier->id,$bulanSebelumnya, $tahunSebelumnya);

        $pdf = PDF::loadview('kas-per-supplier.pdf', [
            'supplier' => $supplier,
            'data' => $data,
            'dataSebelumnya' => $dataSebelumnya,
            'stringBulan' => $stringBulan,
            'tahun' => $tahun,
            'tahunSebelumnya' => $tahunSebelumnya,
            'bulan' => $bulan,
            'stringBulanNow' => $stringBulanNow,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Rekap Kas Supplier '.$stringBulanNow.' '.$tahun.'.pdf');
    }

    public function detail_bayar(InvoiceBayar $invoice)
    {
        $check = Supplier::findOrfail(auth()->user()->supplier_id);

        $data = $invoice->transaksi;
        $supplier = $invoice->supplier;

        if ($check->id !== $supplier->id) {
            abort(404);
        }

        $total = $data->sum('total');
        $totalBerat = $data->sum('berat');
        $totalBayar = $data->sum('total_bayar');

        return view('kas-per-supplier.detail-bayar', [
            'invoice' => $invoice,
            'data' => $data,
            'supplier' => $supplier,
            'totalBerat' => $totalBerat,
            'total' => $total,
            'totalTagihan' => $totalBayar,
        ]);
    }

    public function detail_bayar_print(InvoiceBayar $invoice)
    {
        $check = Supplier::findOrfail(auth()->user()->supplier_id);

        $data = $invoice->transaksi;
        $supplier = $invoice->supplier;

        if ($check->id !== $supplier->id) {
            abort(404);
        }

        $total = $data->sum('total');
        $totalBerat = $data->sum('berat');
        $totalBayar = $data->sum('total_bayar');

        $pdf = PDF::loadview('kas-per-supplier.detail-bayar-pdf', [
            'invoice' => $invoice,
            'data' => $data,
            'supplier' => $supplier,
            'totalBerat' => $totalBerat,
            'total' => $total,
            'totalTagihan' => $totalBayar,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Detail Pembayaran '.$supplier->nama.'.pdf');
    }
}
