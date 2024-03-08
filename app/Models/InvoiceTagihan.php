<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvoiceTagihan extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $appends = ['nf_nilai_tagihan', 'nf_dibayar', 'nf_sisa_tagihan', 'pengeluaran', 'profit'];

    public function kasProjects()
    {
        return $this->hasManyThrough(KasProject::class, Project::class, 'id', 'project_id', 'project_id', 'id');
    }

    public function getPengeluaranAttribute()
    {
        $latestKasProject = $this->kasProjects->last();
        $pengeluaran = $latestKasProject ? $latestKasProject->sisa : 0;
        return $pengeluaran;
    }

    public function getProfitAttribute()
    {
        $profit = $this->nilai_tagihan + $this->pengeluaran;
        return $profit;
    }

    public function getNfPengeluaranAttribute()
    {
        return number_format($this->pengeluaran, 0, ',', '.');
    }

    public function getNfProfitAttribute()
    {
        return number_format($this->profit, 0, ',', '.');
    }

    public function getNfNilaiTagihanAttribute()
    {
        return number_format($this->nilai_tagihan, 0, ',', '.');
    }

    public function getNfDibayarAttribute()
    {
        return number_format($this->dibayar, 0, ',', '.');
    }

    public function getNfSisaTagihanAttribute()
    {
        return number_format($this->sisa_tagihan, 0, ',', '.');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cicilan($invoice_id, $data)
    {

        $db = new KasProject();
        $kb = new KasBesar();
        $invoice = InvoiceTagihan::find($invoice_id);

        $rekening = Rekening::where('untuk', 'kas-besar')->first();
        $data['uraian'] = 'Cicilan '.$invoice->project->nama;
        $data['nominal'] = str_replace('.', '', $data['nominal']);
        $data['bank'] = $rekening->bank;
        $data['no_rek'] = $rekening->no_rek;
        $data['nama_rek'] = $rekening->nama_rek;
        $data['jenis'] = 1;
        $sisa = $db->sisaTerakhir($invoice->project_id) + $data['nominal'];

        DB::beginTransaction();

        $invoice->update([
                            'dibayar' => $invoice->dibayar + $data['nominal'],
                            'sisa_tagihan' => $invoice->sisa_tagihan - $data['nominal']
                        ]);

        $db->create([
            'project_id' => $invoice->project_id,
            'nominal' => $data['nominal'],
            'jenis' => $data['jenis'],
            'sisa' => $sisa,
            'uraian' => $data['uraian'],
            'no_rek' => $data['no_rek'],
            'nama_rek' => $data['nama_rek'],
            'bank' => $data['bank'],
        ]);

        $store = $kb->create([
            'project_id' => $invoice->project_id,
            'nominal' => $data['nominal'],
            'jenis' => $data['jenis'],
            'uraian' => $data['uraian'],
            'no_rek' => $data['no_rek'],
            'nama_rek' => $data['nama_rek'],
            'bank' => $data['bank'],
            'saldo' => $kb->saldoTerakhir() + $data['nominal'],
            'modal_investor_terakhir' => $kb->modalInvestorTerakhir()
        ]);

        DB::commit();

        return $store;

    }

}
