<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceTagihan extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transaksi()
    {
        return $this->hasManyThrough(
            Transaksi::class,
            InvoiceTagihanDetail::class,
            'invoice_tagihan_id',
            'id',
            'id',
            'transaksi_id'
        );
    }

    public function noInvoice()
    {
        return $this->max('no_invoice') + 1 ?? 1;
    }
}
