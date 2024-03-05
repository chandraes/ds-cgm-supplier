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

    public function cicilan($data)
    {
        $db = new KasProject();
        $invoice = InvoiceTagihan::find($data['id']);
        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        $data['nominal'] = str_replace('.', '', $data['nominal']);
        // $data[]

        $invoice->update([
                            'dibayar' => $data['nominal'],
                            'sisa_tagihan' => $invoice->nilai_tagihan - $data['nominal']
                        ]);

    }

}
