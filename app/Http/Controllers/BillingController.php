<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaksi;
use App\Models\Supplier;
use App\Models\InvoicePpn;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $transaksi = new Transaksi();
        $nt = $transaksi->totalNotaTagihan();
        $nb = $transaksi->totalNotaBayar();
        $ip = $transaksi->totalInvoicePpn();
        $ppn = InvoicePpn::where('bayar', false)->count();
        $t = $transaksi->where('status', 1)->get();
        $customer = Customer::all();
        $supplier = Supplier::select('id', 'nama', 'nickname')->get();

        return view('billing.index', [
            'customer' => $customer,
            'nt' => $nt,
            'nb' => $nb,
            'ip' => $ip,
            'ppn' => $ppn,
            'supplier' => $supplier,
            't' => $t,
        ]);
    }
}
