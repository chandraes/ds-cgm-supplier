<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceBayar extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function invoiceBayarDetail()
    {
        return $this->hasMany(InvoiceBayarDetail::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getNoInvoiceAttribute($value)
    {
        return str_pad($value, 2, '0', STR_PAD_LEFT);;
    }

    public function getFormatNoInvoiceAttribute()
    {
        return 'BS' . $this->no_invoice;
    }

    public function transaksi()
    {
        return $this->hasManyThrough(
            Transaksi::class,
            InvoiceBayarDetail::class,
            'invoice_bayar_id',
            'id',
            'id',
            'transaksi_id'
        );
    }

    public function generateNoInvoice()
    {
        return $this->max('no_invoice') + 1;
    }

    public function insertBayar($data)
    {

        $data['tanggal'] = date('Y-m-d');
        $data['no_invoice'] = $this->generateNoInvoice();
        $store = $this->create($data);

        return $store;
    }
}
