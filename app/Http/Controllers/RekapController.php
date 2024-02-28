<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\KasBesar;
use App\Models\Supplier;
use App\Models\KasSupplier;
use App\Models\InvoiceTagihan;
use App\Models\InvoiceBayar;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function index()
    {
        $customer = Customer::all();
        $supplier = Supplier::select('id', 'nama', 'nickname')->get();

        return view('rekap.index', [
            'customer' => $customer,
            'supplier' => $supplier
        ]);
    }

    public function kas_besar(Request $request)
    {
        $kas = new KasBesar;

        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $dataTahun = $kas->dataTahun();

        $data = $kas->kasBesarNow($bulan, $tahun);

        $bulanSebelumnya = $bulan - 1;
        $bulanSebelumnya = $bulanSebelumnya == 0 ? 12 : $bulanSebelumnya;
        $tahunSebelumnya = $bulanSebelumnya == 12 ? $tahun - 1 : $tahun;
        $stringBulan = Carbon::createFromDate($tahun, $bulanSebelumnya)->locale('id')->monthName;
        $stringBulanNow = Carbon::createFromDate($tahun, $bulan)->locale('id')->monthName;

        $dataSebelumnya = $kas->lastKasBesarByMonth($bulanSebelumnya, $tahunSebelumnya);

        return view('rekap.kas-besar.index', [
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

    public function detail_tagihan(InvoiceTagihan $invoice)
    {
        $data = $invoice->transaksi;
        $customer = $invoice->customer;
        $total = $data->sum('total');
        $totalBerat = $data->sum('berat');
        $totalTagihan = $data->sum('total_tagihan');


        return view('rekap.kas-besar.detail-tagihan', [
            'data' => $data,
            'customer' => $customer,
            'totalBerat' => $totalBerat,
            'total' => $total,
            'totalTagihan' => $totalTagihan,
        ]);
    }

    public function detail_bayar(InvoiceBayar $invoice)
    {
        $data = $invoice->transaksi;
        $supplier = $invoice->supplier;
        $total = $data->sum('total');
        $totalBerat = $data->sum('berat');
        $totalBayar = $data->sum('total_bayar');

        return view('rekap.kas-besar.detail-bayar', [
            'data' => $data,
            'supplier' => $supplier,
            'totalBerat' => $totalBerat,
            'total' => $total,
            'totalTagihan' => $totalBayar,
        ]);
    }

    public function detail_bayar_supplier(InvoiceBayar $invoice)
    {
        $data = $invoice->transaksi;
        $supplier = $invoice->supplier;
        $total = $data->sum('total');
        $totalBerat = $data->sum('berat');
        $totalBayar = $data->sum('total_bayar');

        return view('rekap.kas-supplier.detail-bayar', [
            'data' => $data,
            'supplier' => $supplier,
            'totalBerat' => $totalBerat,
            'total' => $total,
            'totalTagihan' => $totalBayar,
            'invoice' => $invoice,
        ]);

    }

    public function detail_bayar_supplier_pdf(InvoiceBayar $invoice)
    {
        $data = $invoice->transaksi;
        $supplier = $invoice->supplier;
        $total = $data->sum('total');
        $totalBerat = $data->sum('berat');
        $totalBayar = $data->sum('total_bayar');

        $pdf = PDF::loadview('rekap.kas-supplier.detail-bayar-pdf', [
            'data' => $data,
            'supplier' => $supplier,
            'totalBerat' => $totalBerat,
            'total' => $total,
            'totalTagihan' => $totalBayar,
            'invoice' => $invoice,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Detail Pembayaran '.$supplier->nama.'.pdf');
    }

    public function kas_besar_print(Request $request)
    {
        $kas = new KasBesar;

        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $dataTahun = $kas->dataTahun();

        $data = $kas->kasBesarNow($bulan, $tahun);

        $bulanSebelumnya = $bulan - 1;
        $bulanSebelumnya = $bulanSebelumnya == 0 ? 12 : $bulanSebelumnya;
        $tahunSebelumnya = $bulanSebelumnya == 12 ? $tahun - 1 : $tahun;
        $stringBulan = Carbon::createFromDate($tahun, $bulanSebelumnya)->locale('id')->monthName;
        $stringBulanNow = Carbon::createFromDate($tahun, $bulan)->locale('id')->monthName;

        $dataSebelumnya = $kas->lastKasBesarByMonth($bulanSebelumnya, $tahunSebelumnya);

        $pdf = PDF::loadview('rekap.kas-besar.pdf', [
            'data' => $data,
            'dataSebelumnya' => $dataSebelumnya,
            'stringBulan' => $stringBulan,
            'tahun' => $tahun,
            'tahunSebelumnya' => $tahunSebelumnya,
            'bulan' => $bulan,
            'stringBulanNow' => $stringBulanNow,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Rekap Kas Besar '.$stringBulanNow.' '.$tahun.'.pdf');
    }

    public function rekap_invoice(Customer $customer, Request $request)
    {

        $transaksi = new Transaksi;

        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $dataTahun = $transaksi->dataTahun();

        $data = $transaksi->rekapInvoice($customer->id, $bulan, $tahun);
        $totalBerat = $data->sum('berat');
        $total = $data->sum('total');
        $totalPPN = $data->sum('total_ppn');
        $totalTagihan = $data->sum('total_tagihan');
        $totalProfit = $data->sum('profit');
        $totalPPH = $data->sum('pph');
        $totalBayar = $data->sum('total_bayar');

        $stringBulanNow = Carbon::createFromDate($tahun, $bulan)->locale('id')->monthName;


        return view('rekap.invoice.index', [
            'customer' => $customer,
            'data' => $data,
            'dataTahun' => $dataTahun,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'stringBulanNow' => $stringBulanNow,
            'totalBerat' => $totalBerat,
            'total' => $total,
            'totalPPN' => $totalPPN,
            'totalTagihan' => $totalTagihan,
            'totalProfit' => $totalProfit,
            'totalPPH' => $totalPPH,
            'totalBayar' => $totalBayar,
        ]);

    }

    public function kas_supplier(Request $request)
    {

        $supplier = Supplier::findOrFail($request->supplier);

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

        return view('rekap.kas-supplier.index', [
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

    public function kas_supplier_print(Request $request)
    {

        $supplier = Supplier::findOrFail($request->supplier);

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

        $pdf = PDF::loadview('rekap.kas-supplier.pdf', [
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
}
