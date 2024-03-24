<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Investor;
use App\Models\InvestorModal;
use App\Models\InvoiceTagihan;
use App\Models\KasBesar;
use App\Models\KasProject;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $nt = InvoiceTagihan::where('cutoff', 0)->where('finished', 0)->count();
        $it = InvoiceTagihan::where('cutoff', 1)->where('finished', 0)->count();

        $ip = InvoiceTagihan::where('cutoff', 1)
                            ->where('ppn', 0)
                            ->where('finished', 1)
                            ->where('nilai_ppn', '>', 0)
                            ->count();

        $np = KasProject::where('ppn_masuk', 1)->count();

        return view('billing.index', [
            'customer' => Customer::all(),
            'nt' => $nt,
            'it' => $it,
            'np' => $np,
            'ip' => $ip,
        ]);
    }

    public function invoice_tagihan()
    {
        $data = InvoiceTagihan::with(['customer', 'project','kasProjects', 'invoiceTagihanDetails'])
                    ->where('cutoff', 1)
                    ->where('finished', 0)
                    ->get();

        return view('billing.invoice-tagihan.index', [
            'data' => $data,
        ]);
    }

    public function nota_ppn_masukan()
    {
        $data = KasProject::with(['project', 'project.customer'])->where('ppn_masuk', 1)->get();

        return view('billing.ppn-masukan.index', [
            'data' => $data,
        ]);
    }

    public function claim_ppn(KasProject $kasProject)
    {
        $db = new KasProject();

        $store = $db->claim_ppn($kasProject);

        return redirect()->back()->with($store['status'], $store['message']);
    }

    public function invoice_ppn()
    {
        $data = InvoiceTagihan::with(['project', 'customer', 'invoiceTagihanDetails'])
                            ->where('cutoff', 1)
                            ->where('ppn', 0)
                            ->where('finished', 1)
                            ->where('nilai_ppn', '>', 0)
                            ->get();

        return view('billing.invoice-ppn.index', [
            'data' => $data,
        ]);
    }

    public function invoice_ppn_bayar(InvoiceTagihan $invoice)
    {
        $db = new InvoiceTagihan();
        $kb = new KasBesar();

        $saldo = $kb->saldoTerakhir();

        if ($saldo < $invoice->nilai_ppn) {
            return redirect()->back()->with('error', 'Saldo kas besar tidak mencukupi');
        }

        $store = $db->invoice_ppn_bayar($invoice);

        return redirect()->back()->with($store['status'], $store['message']);
    }

    public function ppn_masuk_susulan()
    {
        $data = Investor::all();
        $im = InvestorModal::where('persentase', '>', 0)->get();

        $pp = Investor::where('nama', 'pengelola')->first()->persentase;
        $pi = Investor::where('nama', 'investor')->first()->persentase;

        return view('billing.ppn-susulan.index', [
            'data' => $data,
            'im' => $im,
            'pp' => $pp,
            'pi' => $pi,
        ]);
    }

    public function ppn_masuk_susulan_store(Request $request)
    {
        $data = $request->validate([
                    'nominal' => 'required',
                ]);

        $db = new KasBesar();

        $store = $db->ppn_masuk_susulan($data['nominal']);

        return redirect()->back()->with($store['status'], $store['message']);

    }
}
