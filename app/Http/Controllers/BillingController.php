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
        $customer = Customer::all();
        $nt = InvoiceTagihan::where('cutoff', 0)->where('finished', 0)->count();
        $it = InvoiceTagihan::where('cutoff', 1)->where('finished', 0)->count();
        $np = KasProject::where('ppn_masuk', 1)->count();

        return view('billing.index', [
            'customer' => $customer,
            'nt' => $nt,
            'it' => $it,
            'np' => $np,
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
}
