<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KasProject extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['nf_nominal', 'nf_sisa', 'tanggal'];

    public function getNfNominalAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }

    public function getTanggalAttribute()
    {
        return date('d-m-Y', strtotime($this->created_at));
    }

    public function getNfSisaAttribute()
    {
        return number_format($this->sisa, 0, ',', '.');
    }


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function sisaTerakhir($project_id)
    {
        return $this->where('project_id', $project_id)->orderBy('id', 'desc')->first()->sisa ?? 0;
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

    public function transaksiKeluar($data)
    {
        $data['jenis'] = 0;

        DB::beginTransaction();

        $this->create([
            'project_id' => $data['project_id'],
            'nominal' => $data['nominal'],
            'jenis' => $data['jenis'],
            'sisa' => $this->sisaTerakhir($data['project_id']) - $data['nominal'],
            'uraian' => $data['uraian'],
            'no_rek' => $data['no_rek'],
            'nama_rek' => $data['nama_rek'],
            'bank' => $data['bank'],
        ]);

        $db = new KasBesar();

        $data['saldo'] = $db->saldoTerakhir() - $data['nominal'];
        $data['modal_investor_terakhir'] = $db->modalInvestorTerakhir();

        $store = $db->create($data);

        DB::commit();

        return $store;

    }

    public function transaksiMasuk($data)
    {

        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        $data['nominal'] = str_replace('.', '', $data['nominal']);
        $data['jenis'] = 1;
        $data['no_rek'] = $rekening->no_rek;
        $data['nama_rek'] = $rekening->nama_rek;
        $data['bank'] = $rekening->bank;

        DB::beginTransaction();

        $kas = $this->create([
                    'project_id' => $data['project_id'],
                    'nominal' => $data['nominal'],
                    'jenis' => $data['jenis'],
                    'sisa' => $this->sisaTerakhir($data['project_id']) + $data['nominal'],
                    'uraian' => $data['uraian'],
                    'no_rek' => $data['no_rek'],
                    'nama_rek' => $data['nama_rek'],
                    'bank' => $data['bank'],
                ]);

        $db = new KasBesar();

        $data['saldo'] = $db->saldoTerakhir() + $data['nominal'];
        $data['modal_investor_terakhir'] = $db->modalInvestorTerakhir();

        $store = $db->create($data);

        DB::commit();

        return $store;

    }

}
