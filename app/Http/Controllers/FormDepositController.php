<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use App\Services\StarSender;
use App\Models\PesanWa;
use App\Models\GroupWa;
use App\Models\KasProject;
use App\Models\Project;
use Illuminate\Http\Request;

class FormDepositController extends Controller
{
    public function masuk()
    {
        $project = Project::where('project_status_id', 1)->get();

        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        return view('billing.form-deposit.masuk', [
            'rekening' => $rekening,
            'projects' => $project
        ]);
    }

    public function masuk_store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nominal' => 'required',
        ]);

        $db = new KasProject;

        $store = $db->masukDeposit($data);

        $group = GroupWa::where('untuk', 'kas-besar')->first();
        $pesan =    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                    "*Form Permintaan Deposit*\n".
                    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                    "*".$store->project->nama."*\n\n".
                    "Nilai :  *Rp. ".number_format($store->nominal, 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$store->bank."\n".
                    "Nama    : ".$store->nama_rek."\n".
                    "No. Rek : ".$store->no_rek."\n\n".
                    "==========================\n".
                    "Terima kasih 🙏🙏🙏\n";
        $send = new StarSender($group->nama_group, $pesan);
        $res = $send->sendGroup();

        $status = ($res == 'true') ? 1 : 0;

        PesanWa::create([
            'pesan' => $pesan,
            'tujuan' => $group->nama_group,
            'status' => $status,
        ]);

        return redirect()->route('billing')->with('success', 'Berhasil menambahkan data');
    }

    public function keluar()
    {
        $project = Project::where('project_status_id', 1)->get();
        $rekening = Rekening::where('untuk', 'withdraw')->first();

        return view('billing.form-deposit.keluar', [
            'rekening' => $rekening,
            'projects' => $project
        ]);
    }

    public function getModalInvestorProject(Request $request)
    {
        $db = new KasProject;
        $result = $db->modal_investor_project_terakhir($request->project_id) * -1;
        $result = number_format($result, 0, ',', '.');

        return response()->json($result);
    }

    public function keluar_store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nominal' => 'required',
        ]);

        $db = new KasProject;
        $modal = $db->modal_investor_project_terakhir($request->project_id) * -1;

        $data['nominal'] = str_replace('.', '', $data['nominal']);

        if($modal < $data['nominal']){
            return redirect()->back()->with('error', 'Nominal Melebihi Modal Investor Project!!');
        }

        $store = $db->keluarDeposit($data);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        $pesan =    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                    "*Form Pengembalian Deposit*\n".
                    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                    "*".$store->project->nama."*\n\n".
                    "Nilai :  *Rp. ".number_format($store->nominal, 0, ',', '.')."*\n\n".
                    "Ditransfer ke rek:\n\n".
                    "Bank      : ".$store->bank."\n".
                    "Nama    : ".$store->nama_rek."\n".
                    "No. Rek : ".$store->no_rek."\n\n".
                    "==========================\n".
                    "Terima kasih 🙏🙏🙏\n";

        $send = new StarSender($group->nama_group, $pesan);
        $res = $send->sendGroup();

        $status = ($res == 'true') ? 1 : 0;

        PesanWa::create([
            'pesan' => $pesan,
            'tujuan' => $group->nama_group,
            'status' => $status,
        ]);

        return redirect()->route('billing')->with('success', 'Data berhasil disimpan');
    }
}
