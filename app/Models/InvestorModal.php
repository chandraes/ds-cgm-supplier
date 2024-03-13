<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorModal extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $append = ['nf_modal'];

    public function getNfModalAttribute()
    {
        return number_format($this->modal, 0, ',', '.');
    }

    public function kasBesar()
    {
        return $this->hasMany(KasBesar::class);
    }
}
