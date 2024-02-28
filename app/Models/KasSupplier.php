<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasSupplier extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getIdTanggalAttribute()
    {
        return date('d-m-Y', strtotime($this->attributes['tanggal'] ));
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lastKasSupplier($supplier_id)
    {
        return $this->where('supplier_id', $supplier_id)->latest()->orderBy('id', 'desc')->first();
    }

    public function kasSupplierNow($supplier_id, $month, $year)
    {
        return $this->where('supplier_id', $supplier_id)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->get();
    }

    public function lastKasSupplierByMonth($supplier_id, $month, $year)
    {
        return $this->where('supplier_id', $supplier_id)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->latest()->orderBy('id', 'desc')->first();
    }

    public function dataTahun()
    {
        return $this->selectRaw('YEAR(tanggal) tahun')->groupBy('tahun')->get();
    }

    public function insertKeluar($data)
    {
        $data['tanggal'] = date('Y-m-d');
        $data['jenis'] = 0;
        $saldo = $this->lastKasSupplier($data['supplier_id'])->saldo ?? 0;
        $data['saldo'] = $saldo - $data['nominal_transaksi'];

        $store = $this->create($data);
        return $store;
    }

    public function insertBayar($data)
    {
        $data['tanggal'] = date('Y-m-d');
        $data['jenis'] = 1;
        $saldo = $this->lastKasSupplier($data['supplier_id'])->saldo ?? 0;
        $data['saldo'] = $saldo + $data['nominal_transaksi'];

        $store = $this->create($data);
        return $store;
    }

    public function saldoTitipan()
    {
        $supplier = Supplier::all();
        $saldo = 0;

        foreach ($supplier as $v) {
            $saldo += $this->lastKasSupplier($v->id)->saldo ?? 0;
        }

        return $saldo;
    }
}
