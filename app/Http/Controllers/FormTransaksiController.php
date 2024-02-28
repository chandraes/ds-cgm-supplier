<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class FormTransaksiController extends Controller
{
    public function tambah(Customer $customer)
    {
        $transaksi = new Transaksi;
        $data = $transaksi->formTransaksi($customer->id);
        $beratTotal = $transaksi->formTransaksiBerat($customer->id);
        $total = $transaksi->formTransaksiTotal($customer->id);

        $supplier = Supplier::all();

        return view('billing.form-transaksi.index', [
            'data' => $data,
            'customer' => $customer,
            'supplier' => $supplier,
            'beratTotal' => $beratTotal,
            'total' => $total,
        ]);
    }

    public function tambah_store(Request $request)
    {
        $data = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'supplier_id' => 'required|exists:suppliers,id',
                'tanggal' => 'required',
                'nota_timbangan' => [
                    'required',
                    'min:9',
                    'max:9',
                    Rule::unique('transaksis')->where(function ($query) use ($request) {
                        return $query->where('customer_id', $request->customer_id);
                    }),
                ],
                'berat' => 'required',
            ]);

        $tgl = $data['tanggal'];
        $supplier = Supplier::findOrFail($data['supplier_id']);

        if ($supplier->persen_profit == null || $supplier->persen_profit == 0) {
            return redirect()->back()->with('error', 'Supplier belum memiliki persen profit! Harap hubungi admin untuk mengisi persen profit supplier!');
        }

        $persen_profit = $supplier->persen_profit / 100;

        $customer = Customer::findOrFail($data['customer_id']);
        $data['berat'] = str_replace('.', '', $data['berat']);
        $data['tanggal'] = date('Y-m-d', strtotime($data['tanggal']));
        $data['harga'] = str_replace('.', '', $customer->harga);
        $data['total'] = $data['berat'] * $data['harga'];
        $data['pph'] = $data['total'] * 0.0025;
        $data['profit'] = $data['total'] * $persen_profit;
        $data['total_ppn'] = $data['total'] * 0.11;
        $data['total_tagihan'] = $data['total'] - $data['pph'];
        $data['total_bayar'] = $data['total_tagihan'] - $data['profit'];

        $store = Transaksi::create($data);

        return redirect()->route('form-transaksi.tambah', ['customer' => $customer->id])->with(['success' => 'Data Berhasil Ditambahkan', 'tgl' => $tgl, 'supplier' => $data['supplier_id']]);

    }

    public function edit_store(Request $request, Transaksi $transaksi)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required',
            'nota_timbangan' => 'required|min:9|max:9',
            'berat' => 'required',
        ]);

        $supplier = Supplier::findOrFail($data['supplier_id']);

        if ($supplier->persen_profit == null || $supplier->persen_profit == 0) {
            return redirect()->back()->with('error', 'Supplier belum memiliki persen profit! Harap hubungi admin untuk mengisi persen profit supplier!');
        }

        $persen_profit = $supplier->persen_profit / 100;

        $data['berat'] = str_replace('.', '', $data['berat']);
        $data['tanggal'] = date('Y-m-d', strtotime($data['tanggal']));
        $data['total'] = $data['berat'] * $transaksi->harga;
        $data['pph'] = $data['total'] * 0.0025;
        $data['profit'] = $data['total'] * $persen_profit;
        $data['total_ppn'] = $data['total'] * 0.11;
        $data['total_tagihan'] = $data['total'] - $data['pph'];
        $data['total_bayar'] = $data['total_tagihan'] - $data['profit'];

        try {

            $transaksi->update($data);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Terdapat Nota Timbang yang sama!');
        }

        return redirect()->back()->with('success', 'Data Berhasil Diubah');
    }

    public function delete(Transaksi $transaksi)
    {
        $transaksi->delete();

        return redirect()->back()->with('success', 'Data Berhasil Dihapus');
    }

    public function lanjutkan(Customer $customer)
    {
        // update status to 1 for all transaksi with customer_id = $customer->id and status = 0
        $data = Transaksi::where('customer_id', $customer->id)->where('status', 0)->update(['status' => 1]);

        return redirect()->route('billing')->with('success', 'Data Berhasil Disimpan');
    }
}
