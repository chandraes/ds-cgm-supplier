<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $customer = Customer::all();

        return view('billing.index', [
            'customer' => $customer,
        ]);
    }
}
