<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KasKecil extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $appends = ['nf_nominal', 'tanggal'];

    public function getNfNominalAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }

    public function getTanggalAttribute()
    {
        return date('d-m-Y', strtotime($this->created_at));
    }

    public function saldoTerakhir()
    {
        return $this->orderBy('id', 'desc')->first()->saldo ?? 0;
    }

    public function kasKecil($month, $year)
    {
        return $this->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
    }

    public function kasKecilByMonth($month, $year)
    {
        $data = $this->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if (!$data) {
            $data = $this->where('created_at', '<', Carbon::create($year, $month, 1))
                ->orderBy('id', 'desc')
                ->first();
        }

        return $data;
    }

    public function masukKasKecil($data)
    {
        $db = new KasBesar();

        DB::beginTransaction();

        $kb = $db->keluarKasKecil();

        $data['nominal'] = 1000000;
        $data['saldo'] = $this->saldoTerakhir() + $data['nominal'];
        $data['jenis'] = 1;
        $data['nomor_kode_kas_kecil'] = $kb->nomor_kode_kas_kecil;
        $data['nama_rek'] = $kb->nama_rek;
        $data['bank'] = $kb->bank;
        $data['no_rek'] = $kb->no_rek;

        $store = $this->create($data);

        DB::commit();

        return $store;


    }
}
