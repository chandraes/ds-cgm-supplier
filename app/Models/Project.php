<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['id_tanggal_mulai', 'id_jatuh_tempo', 'nf_nilai', 'kode_project', 'total_tagihan', 'nf_total_tagihan'];

    public function kas_project()
    {
        return $this->hasMany(KasProject::class);
    }

    public function getTotalTagihanAttribute()
    {
        $total = $this->nilai + ($this->nilai_ppn) - ($this->nilai_pph);
        return $total;
    }

    public function getNfTotalTagihanAttribute()
    {
        return number_format($this->total_tagihan, 0, ',', '.');
    }

    public function invoice_tagihan()
    {
        return $this->hasOne(InvoiceTagihan::class);
    }

    public static function generateKode()
    {
        $kode = Project::max('kode') + 1;
        return $kode;
    }

    public function getKodeProjectAttribute()
    {
        return 'P' . str_pad($this->kode, 2, '0', STR_PAD_LEFT);
    }

    public function getNfNilaiAttribute()
    {
        return number_format($this->nilai, 0, ',', '.');
    }

    public function getIdTanggalMulaiAttribute()
    {
        return Carbon::parse($this->tanggal_mulai)->format('d-m-Y');
    }

    public function getIdJatuhTempoAttribute()
    {
        return Carbon::parse($this->jatuh_tempo)->format('d-m-Y');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function project_status()
    {
        return $this->belongsTo(ProjectStatus::class);
    }

    public function id_tanggal_mulai()
    {
        return Carbon::parse($this->tanggal_mulai)->format('d-m-Y');
    }

    public function id_jatuh_tempo()
    {
        return Carbon::parse($this->jatuh_tempo)->format('d-m-Y');
    }

    public static function createProject($data)
    {
        $data['nilai'] = str_replace('.', '', $data['nilai']);
        $date = Carbon::createFromFormat('d-m-Y', $data['tanggal_mulai']);
        $data['tanggal_mulai'] = $date->format('Y-m-d');
        $jatuhTempo = Carbon::createFromFormat('d-m-Y', $data['jatuh_tempo']);
        $data['jatuh_tempo'] = $jatuhTempo->format('Y-m-d');
        $data['project_status_id'] = 1;
        $data['kode'] = Project::generateKode();

        DB::beginTransaction();

        try {
            $store = Project::create($data);

            $ppn = ($store->ppn == 1) ? $store->nilai * 0.11 : 0;
            $pph = ($store->pph == 1) ? $store->nilai * 0.02 : 0;
            $sisa_tagihan = $store->nilai + $ppn - $pph;

            $invoice = InvoiceTagihan::create([
                'customer_id' => $data['customer_id'],
                'project_id' => $store->id,
                'nilai_tagihan' => $data['nilai'],
                'nilai_ppn' => $ppn,
                'nilai_pph' => $pph,
                'sisa_tagihan' => $sisa_tagihan,
                'dibayar' => 0,
            ]);

            DB::commit();

            $response = [
                'status' => 'success',
                'message' => 'Data berhasil disimpan!!',
                'data' => $store,
            ];

        } catch (\Throwable $th) {
            DB::rollBack();

            $response = [
                'status' => 'error',
                'message' => 'Data gagal disimpan!!',
                'data' => $th->getMessage(),
            ];

        }

        return $response;

    }

    public static function updateProject($id, $data)
    {
        $data['nilai'] = str_replace('.', '', $data['nilai']);
        $data['tanggal_mulai'] = Carbon::createFromFormat('d-m-Y', $data['tanggal_mulai'])->format('Y-m-d');
        $data['jatuh_tempo'] = Carbon::createFromFormat('d-m-Y', $data['jatuh_tempo'])->format('Y-m-d');

        $project = Project::find($id);

        if ($project) {
            $project->update($data);

            // Retrieve the updated project
            $updatedProject = Project::find($id);

            $ppn = ($updatedProject->ppn == 1) ? $updatedProject->nilai * 0.11 : 0;
            $pph = ($updatedProject->pph == 1) ? $updatedProject->nilai * 0.02 : 0;
            $sisa_tagihan = $updatedProject->nilai + $ppn - $pph;

            $invoice = InvoiceTagihan::where('project_id', $id)->first();

            $invoice->update([
                'nilai_tagihan' => $data['nilai'],
                'nilai_ppn' => $ppn,
                'nilai_pph' => $pph,
                'sisa_tagihan' => $sisa_tagihan - $invoice->dibayar
            ]);

        }

        return $project;
    }
}
