<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GroupWa;
use App\Models\InvestorModal;
use App\Models\InvoiceTagihan;
use App\Models\KasBesar;
use App\Models\KasKecil;
use App\Models\KasProject;
use App\Models\PesanWa;
use App\Models\Project;
use App\Services\StarSender;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function index()
    {
        $customer = Customer::all();
        $project = Project::where('project_status_id', 1)->get();

        return view('rekap.index', [
            'customer' => $customer,
            'project' => $project,
        ]);
    }

    public function kas_besar(Request $request)
    {
        $kas = new KasBesar();

        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $dataTahun = $kas->dataTahun();

        $data = $kas->kasBesar($bulan, $tahun);

        $bulanSebelumnya = $bulan - 1;
        $bulanSebelumnya = $bulanSebelumnya == 0 ? 12 : $bulanSebelumnya;
        $tahunSebelumnya = $bulanSebelumnya == 12 ? $tahun - 1 : $tahun;
        $stringBulan = Carbon::createFromDate($tahun, $bulanSebelumnya)->locale('id')->monthName;
        $stringBulanNow = Carbon::createFromDate($tahun, $bulan)->locale('id')->monthName;

        $dataSebelumnya = $kas->kasBesarByMonth($bulanSebelumnya, $tahunSebelumnya);

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

    public function kas_besar_print(Request $request)
    {
        $kas = new KasBesar();

        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $data = $kas->kasBesar($bulan, $tahun);

        $bulanSebelumnya = $bulan - 1;
        $bulanSebelumnya = $bulanSebelumnya == 0 ? 12 : $bulanSebelumnya;
        $tahunSebelumnya = $bulanSebelumnya == 12 ? $tahun - 1 : $tahun;
        $stringBulan = Carbon::createFromDate($tahun, $bulanSebelumnya)->locale('id')->monthName;
        $stringBulanNow = Carbon::createFromDate($tahun, $bulan)->locale('id')->monthName;

        $dataSebelumnya = $kas->kasBesarByMonth($bulanSebelumnya, $tahunSebelumnya);

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

    public function kas_project(Request $request)
    {
        $project = Project::findOrFail($request->project);

        $kas = new KasProject();

        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $dataTahun = $kas->dataTahun();

        $data = $kas->kasProject($project->id, $bulan, $tahun);

        $bulanSebelumnya = $bulan - 1;
        $bulanSebelumnya = $bulanSebelumnya == 0 ? 12 : $bulanSebelumnya;
        $tahunSebelumnya = $bulanSebelumnya == 12 ? $tahun - 1 : $tahun;
        $stringBulan = Carbon::createFromDate($tahun, $bulanSebelumnya)->locale('id')->monthName;
        $stringBulanNow = Carbon::createFromDate($tahun, $bulan)->locale('id')->monthName;

        $dataSebelumnya = $kas->kasProjectByMonth($project->id, $bulanSebelumnya, $tahunSebelumnya);

        return view('rekap.kas-project.index', [
            'data' => $data,
            'project' => $project,
            'dataTahun' => $dataTahun,
            'dataSebelumnya' => $dataSebelumnya,
            'stringBulan' => $stringBulan,
            'tahun' => $tahun,
            'tahunSebelumnya' => $tahunSebelumnya,
            'bulan' => $bulan,
            'stringBulanNow' => $stringBulanNow,
        ]);
    }

    public function kas_project_print(Request $request)
    {
        $kas = new KasProject();
        $project = Project::findOrFail($request->project);

        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $data = $kas->kasProject($request->project,$bulan, $tahun);

        $bulanSebelumnya = $bulan - 1;
        $bulanSebelumnya = $bulanSebelumnya == 0 ? 12 : $bulanSebelumnya;
        $tahunSebelumnya = $bulanSebelumnya == 12 ? $tahun - 1 : $tahun;
        $stringBulan = Carbon::createFromDate($tahun, $bulanSebelumnya)->locale('id')->monthName;
        $stringBulanNow = Carbon::createFromDate($tahun, $bulan)->locale('id')->monthName;

        $dataSebelumnya = $kas->kasProjectByMonth($request->project, $bulanSebelumnya, $tahunSebelumnya);

        $pdf = PDF::loadview('rekap.kas-project.pdf', [
            'data' => $data,
            'project' => $project,
            'dataSebelumnya' => $dataSebelumnya,
            'stringBulan' => $stringBulan,
            'tahun' => $tahun,
            'tahunSebelumnya' => $tahunSebelumnya,
            'bulan' => $bulan,
            'stringBulanNow' => $stringBulanNow,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Rekap Kas Project '.$stringBulanNow.' '.$tahun.'.pdf');
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


    public function kas_kecil(Request $request)
    {
        $kas = new KasKecil();

        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $dataTahun = $kas->dataTahun();

        $data = $kas->kasKecil($bulan, $tahun);

        $bulanSebelumnya = $bulan - 1;
        $bulanSebelumnya = $bulanSebelumnya == 0 ? 12 : $bulanSebelumnya;
        $tahunSebelumnya = $bulanSebelumnya == 12 ? $tahun - 1 : $tahun;
        $stringBulan = Carbon::createFromDate($tahun, $bulanSebelumnya)->locale('id')->monthName;
        $stringBulanNow = Carbon::createFromDate($tahun, $bulan)->locale('id')->monthName;

        $dataSebelumnya = $kas->kasKecilByMonth($bulanSebelumnya, $tahunSebelumnya);

        return view('rekap.kas-kecil.index', [
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

    public function kas_kecil_print(Request $request)
    {
        $kas = new KasKecil();

        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $dataTahun = $kas->dataTahun();

        $data = $kas->kasKecil($bulan, $tahun);

        $bulanSebelumnya = $bulan - 1;
        $bulanSebelumnya = $bulanSebelumnya == 0 ? 12 : $bulanSebelumnya;
        $tahunSebelumnya = $bulanSebelumnya == 12 ? $tahun - 1 : $tahun;
        $stringBulan = Carbon::createFromDate($tahun, $bulanSebelumnya)->locale('id')->monthName;
        $stringBulanNow = Carbon::createFromDate($tahun, $bulan)->locale('id')->monthName;

        $dataSebelumnya = $kas->kasKecilByMonth($bulanSebelumnya, $tahunSebelumnya);

        $pdf = PDF::loadview('rekap.kas-kecil.pdf', [
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

    public function void_kas_kecil(KasKecil $kas)
    {
        $db = new KasKecil();

        $store = $db->voidKasKecil($kas->id);

        $group = GroupWa::where('untuk', 'team')->first();

        $pesan =    "==========================\n".
                    "*Form Void Kas Kecil*\n".
                    "==========================\n\n".
                    "Uraian: ".$store->uraian."\n\n".
                    "Nilai : *Rp. ".number_format($store->nominal)."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$store->bank."\n".
                    "Nama    : ".$store->nama_rek."\n".
                    "No. Rek : ".$store->no_rek."\n\n".
                    "==========================\n".
                    "Sisa Saldo Kas Kecil : \n".
                    "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
                    "Terima kasih 🙏🙏🙏\n";

        $send = new StarSender($group->nama_group, $pesan);
        $res = $send->sendGroup();

        $status = ($res == 'true') ? 1 : 0;

        PesanWa::create([
            'pesan' => $pesan,
            'tujuan' => $group->nama_group,
            'status' => $status,
        ]);

        return redirect()->back()->with('success', 'Data berhasil di void');
    }

    public function rekap_invoice()
    {
        $data = InvoiceTagihan::with(['invoiceTagihanDetails'])->where('finished', 1)->get();

        return view('rekap.invoice.index', [
            'data' => $data,
        ]);
    }

    public function rekap_investor()
    {
        $data = InvestorModal::all();

        return view('rekap.kas-investor.index', [
            'data' => $data,
        ]);
    }

    public function rekap_investor_detail(InvestorModal $investor, Request $request)
    {
        $data = $investor->load('kasBesar');

        $data->kasBesar->each(function ($d) use (&$total) {
            $d->jenis == 1 ? $total += $d->nominal : $total -= $d->nominal;
        });

        return view('rekap.kas-investor.detail', [
            'data' => $data,
            'total' => $total,
        ]);
    }
}
