<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KasProject extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['nf_nominal', 'nf_saldo_project', 'tanggal', 'nf_saldo'];

    public function getNfNominalAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }

    public function getTanggalAttribute()
    {
        return date('d-m-Y', strtotime($this->created_at));
    }

    public function getNfSaldoAttribute()
    {
        return number_format($this->saldo, 0, ',', '.');
    }

    public function getNfSaldoProjectAttribute()
{
    return number_format($this->saldo_project, 0, ',', '.');
}

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function saldo_terakhir()
    {
        return $this->orderBy('id', 'desc')->first()->saldo ?? 0;
    }

    public function saldo_project_terakhir($project_id)
    {
        return $this->where('project_id', $project_id)->orderBy('id', 'desc')->first()->saldo_project ?? 0;
    }

    public function modal_investor_terakhir()
    {
        return $this->orderBy('id', 'desc')->first()->modal_investor_terakhir ?? 0;
    }

    public function modal_investor_project_terakhir($project_id)
    {
        return $this->where('project_id', $project_id)->orderBy('id', 'desc')->first()->modal_investor_project_terakhir ?? 0;
    }

    public function dataTahun()
    {
        return $this->selectRaw('YEAR(created_at) as tahun')->groupBy('tahun')->get();
    }

    public function kasProject($project_id, $bulan, $tahun)
    {
        return $this->where('project_id', $project_id)
                    ->whereMonth('created_at', $bulan)
                    ->whereYear('created_at', $tahun)
                    ->get();
    }

    public function kasProjectByMonth($project_id, $bulan, $tahun)
    {
        $data = $this->where('project_id', $project_id)
                    ->whereMonth('created_at', $bulan)
                    ->whereYear('created_at', $tahun)
                    ->orderBy('id', 'desc')
                    ->first();

        if (!$data) {
            $data = $this->where('project_id', $project_id)
                    ->where('created_at', '<', Carbon::create($tahun, $bulan, 1))
                    ->orderBy('id', 'desc')
                    ->first();
        }

        return $data;
    }

    public function kasTotal($bulan, $tahun)
    {
        return $this->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->get();
    }

    public function kasTotalByMonth($bulan, $tahun)
    {
        $data = $this->whereMonth('created_at', $bulan)
                    ->whereYear('created_at', $tahun)
                    ->orderBy('id', 'desc')
                    ->first();

        if (!$data) {
        $data = $this->where('created_at', '<', Carbon::create($tahun, $bulan, 1))
                    ->orderBy('id', 'desc')
                    ->first();
        }

        return $data;
    }

    public function masukDeposit($data)
    {
        // dd($data);
        $db = new KasProject();
        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        $data['uraian'] = "Deposit ". substr(Project::find($data['project_id'])->nama, 0, 20);
        $data['no_rek'] = $rekening->no_rek;
        $data['nama_rek'] = $rekening->nama_rek;
        $data['bank'] = $rekening->bank;
        $data['jenis_transaksi'] = 1;
        $data['nominal'] = str_replace('.', '', $data['nominal']);
        $data['saldo'] = $db->saldo_terakhir() + $data['nominal'];
        $data['saldo_project'] = $db->saldo_project_terakhir($data['project_id']) + $data['nominal'];
        $data['modal_investor'] = $data['nominal'];
        $data['modal_investor_terakhir'] = $db->modal_investor_terakhir() - $data['nominal'];
        $data['modal_investor_project'] = $data['nominal'];
        $data['modal_investor_project_terakhir'] = $db->modal_investor_project_terakhir($data['project_id']) - $data['nominal'];

        $store = $this->create($data);

        return $store;

    }

    public function keluarDeposit($data)
    {
        $db = new KasProject();
        $rekening = Rekening::where('untuk', 'withdraw')->first();
    }
}
