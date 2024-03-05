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
        $nt = InvoiceTagihan::where('finished', 0)->count();
        
        return view('billing.index', [
            'customer' => $customer,
            'nt' => $nt,
        ]);
    }
}
