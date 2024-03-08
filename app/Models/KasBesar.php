<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KasBesar extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $appends = ['nf_nominal', 'tanggal', 'kode_deposit', 'kode_kas_kecil', 'nf_saldo'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function dataTahun()
    {
        return $this->selectRaw('YEAR(created_at) as tahun')->groupBy('tahun')->get();
    }

    public function getKodeDepositAttribute()
    {
        return $this->nomor_deposit != null ? 'D'.str_pad($this->nomor_deposit, 2, '0', STR_PAD_LEFT) : '';
    }

    public function getNfSaldoAttribute()
    {
        return number_format($this->saldo, 0, ',', '.');
    }

    public function getKodeKasKecilAttribute()
    {
        return $this->nomor_kode_kas_kecil != null ? 'KK'.str_pad($this->nomor_kode_kas_kecil, 2, '0', STR_PAD_LEFT) : '';
    }

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

    public function modalInvestorTerakhir()
    {
        return $this->orderBy('id', 'desc')->first()->modal_investor_terakhir ?? 0;
    }

    public function kasBesar($month, $year)
    {
        return $this->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
    }

    public function kasBesarByMonth($month, $year)
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

    public function deposit($data)
    {
        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        $data['nomor_deposit'] = $this->max('nomor_deposit') + 1;
        $data['saldo'] = $this->saldoTerakhir() + $data['nominal'];
        $data['modal_investor'] = -$data['nominal'];
        $data['modal_investor_terakhir'] = $this->modalInvestorTerakhir() - $data['nominal'];
        $data['jenis'] = 1;
        $data['no_rek'] = $rekening->no_rek;
        $data['bank'] = $rekening->bank;
        $data['nama_rek'] = $rekening->nama_rek;

        $store = $this->create($data);

        return $store;
    }

    public function withdraw($data)
    {
        $rekening = Rekening::where('untuk', 'withdraw')->first();
        $data['uraian'] = "Withdraw";
        $data['nominal'] = str_replace('.', '', $data['nominal']);
        $data['saldo'] = $this->saldoTerakhir() - $data['nominal'];
        $data['modal_investor'] = $data['nominal'];
        $data['modal_investor_terakhir'] = $this->modalInvestorTerakhir() + $data['nominal'];
        $data['jenis'] = 0;
        $data['no_rek'] = $rekening->no_rek;
        $data['bank'] = $rekening->bank;
        $data['nama_rek'] = $rekening->nama_rek;

        $store = $this->create($data);

        return $store;
    }

    public function keluarKasKecil()
    {
        $rekening = Rekening::where('untuk', 'kas-kecil')->first();
        $data['nominal'] = 1000000;
        $data['nomor_kode_kas_kecil'] = $this->max('nomor_kode_kas_kecil') + 1;
        $data['saldo'] = $this->saldoTerakhir() - $data['nominal'];
        $data['modal_investor_terakhir'] = $this->modalInvestorTerakhir();
        $data['jenis'] = 0;
        $data['no_rek'] = $rekening->no_rek;
        $data['bank'] = $rekening->bank;
        $data['nama_rek'] = $rekening->nama_rek;

        $store = $this->create($data);

        return $store;
    }

    public function lainMasuk($data)
    {
        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        $data['nominal'] = str_replace('.', '', $data['nominal']);
        $data['saldo'] = $this->saldoTerakhir() + $data['nominal'];
        $data['jenis'] = 1;
        $data['no_rek'] = $rekening->no_rek;
        $data['bank'] = $rekening->bank;
        $data['nama_rek'] = $rekening->nama_rek;
        $data['modal_investor_terakhir'] = $this->modalInvestorTerakhir();

        $store = $this->create($data);

        return $store;
    }

    public function lainKeluar($data)
    {

        $data['saldo'] = $this->saldoTerakhir() - $data['nominal'];
        $data['modal_investor_terakhir'] = $this->modalInvestorTerakhir();
        $data['jenis'] = 0;

        $store = $this->create($data);

        return $store;
    }


}
