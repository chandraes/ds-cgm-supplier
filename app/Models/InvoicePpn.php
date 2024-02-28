<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePpn extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function invoicePpnDetail()
    {
        return $this->hasMany(InvoicePpnDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getIdTanggalAttribute()
    {
        return date('d-m-Y', strtotime($this->tanggal));
    }

    public function noInvoice()
    {
        return $this->max('no_invoice') + 1 ?? 1;
    }

    public function transaksi()
    {
        return $this->hasManyThrough(
            Transaksi::class,
            InvoicePpnDetail::class,
            'invoice_ppn_id',
            'id',
            'id',
            'transaksi_id'
        );
    }
}
