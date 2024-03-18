<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InvoiceTagihan;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $customer = Customer::all();
        $nt = InvoiceTagihan::where('cutoff', 0)->where('finished', 0)->count();
        $it = InvoiceTagihan::where('cutoff', 1)->where('finished', 0)->count();

        return view('billing.index', [
            'customer' => $customer,
            'nt' => $nt,
            'it' => $it,
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
}
