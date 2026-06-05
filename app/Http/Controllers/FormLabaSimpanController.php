<?php

namespace App\Http\Controllers;

use App\Models\GroupWa;
use App\Models\KasBesar;
use App\Models\LabaSimpan;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormLabaSimpanController extends Controller
{
    public function masuk()
    {
        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        return view('billing.laba-simpan.masuk', [
            'rekening' => $rekening,
        ]);
    }

    public function masuk_store(Request $request)
    {
        $data = $request->validate([
            'uraian' => 'required|string|max:255',
            'nominal' => 'required',
        ]);

        $data['nominal'] = (int) str_replace(['.', ','], ['', ''], $data['nominal']);

        $rekening = Rekening::where('untuk', 'kas-besar')->first();
        $data['nama_rek'] = $rekening->nama_rek;
        $data['bank'] = $rekening->bank;
        $data['no_rek'] = $rekening->no_rek;

        DB::beginTransaction();

        $currentSaldo = LabaSimpan::orderBy('id', 'desc')->first()->saldo ?? 0;
        $data['jenis'] = 'in';
        $data['saldo'] = $currentSaldo + $data['nominal'];

        $store = LabaSimpan::create($data);
        $kasBesar = new KasBesar;
        $kasBesar->lainMasuk([
            'uraian' => 'Laba Simpan Masuk - '.$data['uraian'],
            'nominal' => $data['nominal'],
        ]);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        if ($group) {
            $pesan = "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                "*Form Laba Disimpan (Dana Masuk)*\n".
                "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                'Uraian :  '.$store->uraian."\n".
                'Nilai :  *Rp. '.number_format($store->nominal, 0, ',', '.')."*\n\n".
                "Disimpan ke rek:\n\n".
                'Bank      : '.$store->bank."\n".
                'Nama    : '.$store->nama_rek."\n".
                'No. Rek : '.$store->no_rek."\n\n".
                "==========================\n".
                "Sisa Saldo Laba Simpan : \n".
                'Rp. '.number_format($store->saldo, 0, ',', '.')."\n\n".
                "Terima kasih 🙏🙏🙏\n";

            $group->sendWa($group->nama_group, $pesan);
        }

        DB::commit();

        return redirect()->route('billing')->with('success', 'Data Laba Simpan berhasil ditambahkan');
    }

    public function keluar()
    {
        return view('billing.laba-simpan.keluar');
    }

    public function keluar_store(Request $request)
    {
        $data = $request->validate([
            'uraian' => 'required|string|max:255',
            'nominal' => 'required',
            'nama_rek' => 'required|string|max:100',
            'bank' => 'required|string|max:100',
            'no_rek' => 'required|string|max:30',
        ]);

        $data['nominal'] = (int) str_replace(['.', ','], ['', ''], $data['nominal']);
        $saldoLaba = LabaSimpan::orderBy('id', 'desc')->first()->saldo ?? 0;

        if ($saldoLaba < $data['nominal']) {
            return redirect()->back()->with('error', 'Saldo Laba Simpan tidak mencukupi');
        }

        DB::beginTransaction();

        $data['jenis'] = 'out';
        $data['saldo'] = $saldoLaba - $data['nominal'];

        $store = LabaSimpan::create($data);
        $kasBesar = new KasBesar;
        $kasBesar->lainKeluar([
            'uraian' => 'Laba Simpan Keluar - '.$data['uraian'],
            'nominal' => $data['nominal'],
            'nama_rek' => $data['nama_rek'],
            'bank' => $data['bank'],
            'no_rek' => $data['no_rek'],
        ]);

        $group = GroupWa::where('untuk', 'kas-besar')->first();

        if ($group) {
            $pesan = "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                "*Form Laba Disimpan (Dana Keluar)*\n".
                "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                'Uraian :  '.$store->uraian."\n".
                'Nilai :  *Rp. '.number_format($store->nominal, 0, ',', '.')."*\n\n".
                "Ditransfer ke rek:\n\n".
                'Bank      : '.$store->bank."\n".
                'Nama    : '.$store->nama_rek."\n".
                'No. Rek : '.$store->no_rek."\n\n".
                "==========================\n".
                "Sisa Saldo Laba Simpan : \n".
                'Rp. '.number_format($store->saldo, 0, ',', '.')."\n\n".
                "Terima kasih 🙏🙏🙏\n";

            $group->sendWa($group->nama_group, $pesan);
        }

        DB::commit();

        return redirect()->route('billing')->with('success', 'Transaksi Laba Simpan berhasil disimpan');
    }
}
