<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasBesar extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function lastKasBesar()
    {
        return $this->latest()->orderBy('id', 'desc')->first();
    }


    public function getIdTanggalAttribute()
    {
        return date('d-m-Y', strtotime($this->tanggal));
    }

    public function lastKasBesarByMonth($month, $year)
    {
        return $this->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->latest()->orderBy('id', 'desc')->first();
    }

    public function kasBesarNow($month, $year)
    {
        return $this->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->get();
    }

    public function dataTahun()
    {
        return $this->selectRaw('YEAR(tanggal) tahun')->groupBy('tahun')->get();
    }

    public function nomorDeposit()
    {
        $lastNomor = $this->whereNotNull('nomor_deposit')->latest()->orderBy('id', 'desc')->first();

        if ($lastNomor) {
            return $lastNomor->nomor_deposit + 1;
        } else {
            return 1;
        }
    }

    public function nomorTitipan()
    {
        $lastNomor = $this->whereNotNull('nomor_titipan')->latest()->orderBy('id', 'desc')->first();

        if ($lastNomor) {
            return $lastNomor->nomor_titipan + 1;
        } else {
            return 1;
        }
    }

    public function getNomorTitipanAttribute($value)
    {
        return str_pad($value, 2, '0', STR_PAD_LEFT);
    }

    // getNomorDepositAttribute
    public function getNomorDepositAttribute($value)
    {
        return str_pad($value, 2, '0', STR_PAD_LEFT);
    }

    public function getNomorTagihanAttribute($value)
    {
        return str_pad($value, 2, '0', STR_PAD_LEFT);
    }

    public function getNomorBayarAttribute($value)
    {
        return str_pad($value, 2, '0', STR_PAD_LEFT);
    }

    public function insertTagihan($data)
    {
        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        $data['tanggal'] = now();
        $data['jenis'] = 1;
        $data['saldo'] = $this->lastKasBesar()->saldo + $data['nominal_transaksi'];
        $data['modal_investor_terakhir'] = $this->lastKasBesar()->modal_investor_terakhir;
        $data['no_rek'] = $rekening->no_rek;
        $data['bank'] = $rekening->bank;
        $data['nama_rek'] = $rekening->nama_rek;

        $store = $this->create($data);
        return $store;
    }

    public function insertBayar($data)
    {
        $supplier = Supplier::findOrFail($data['supplier_id']);
        unset($data['supplier_id']);

        $data['uraian'] = 'Pembayaran ' . $supplier->nickname;
        $data['tanggal'] = now();
        $data['jenis'] = 0;
        $data['saldo'] = $this->lastKasBesar()->saldo - $data['nominal_transaksi'];
        $data['modal_investor_terakhir'] = $this->lastKasBesar()->modal_investor_terakhir;
        $data['no_rek'] = $supplier->no_rek;
        $data['bank'] = $supplier->bank;
        $data['nama_rek'] = $supplier->nama_rek;

        $store = $this->create($data);

        return $store;
    }

    public function lainKeluar($data)
    {
        $data['tanggal'] = now();
        $data['jenis'] = 0;
        $data['saldo'] = $this->lastKasBesar()->saldo - $data['nominal_transaksi'];
        $data['modal_investor_terakhir'] = $this->lastKasBesar()->modal_investor_terakhir;

        $store = $this->create($data);

        return $store;
    }

    public function insertMasuk($data)
    {
        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        $data['tanggal'] = now();
        $data['jenis'] = 1;
        $data['saldo'] = $this->lastKasBesar()->saldo + $data['nominal_transaksi'];
        $data['modal_investor_terakhir'] = $this->lastKasBesar()->modal_investor_terakhir;
        $data['no_rek'] = $rekening->no_rek;
        $data['bank'] = $rekening->bank;
        $data['nama_rek'] = $rekening->nama_rek;

        $store = $this->create($data);

        return $store;
    }

    public function bayarPpn($data)
    {
        $data['tanggal'] = now();
        $data['jenis'] = 0;
        $data['saldo'] = $this->lastKasBesar()->saldo - $data['nominal_transaksi'];
        $data['modal_investor_terakhir'] = $this->lastKasBesar()->modal_investor_terakhir;

        $store = $this->create($data);

        return $store;
    }

}
