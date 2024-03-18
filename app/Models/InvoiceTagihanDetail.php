<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceTagihanDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['nf_nominal', 'tanggal'];



    public function invoiceTagihan()
    {
        return $this->belongsTo(InvoiceTagihan::class);
    }

    public function getNfNominalAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }

    public function getTanggalAttribute()
    {
        return $this->created_at->format('d-m-Y');
    }
}
