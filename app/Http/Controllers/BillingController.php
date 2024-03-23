<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InvoiceTagihan;
use App\Models\KasProject;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $invoiceTagihanQuery = InvoiceTagihan::where('finished', 0);
        $nt = $invoiceTagihanQuery->where('cutoff', 0)->count();
        $it = $invoiceTagihanQuery->where('cutoff', 1)->count();

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
}
