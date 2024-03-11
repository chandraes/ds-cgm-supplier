<?php

namespace App\Models;

use App\Services\StarSender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceTagihan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['nf_nilai_tagihan', 'nf_dibayar', 'nf_sisa_tagihan', 'pengeluaran', 'profit', 'profit_akhir', 'nf_profit_akhir',
                            'bulan_akhir', 'tahun_akhir'];

    public function kasProjects()
    {
        return $this->hasManyThrough(KasProject::class, Project::class, 'id', 'project_id', 'project_id', 'id');
    }

    public function getBulanAkhirAttribute()
    {
        $bulan = $this->kasProjects->last() ? Carbon::parse($this->kasProjects->last()->create_at)->format('m') : date('m');
        return $bulan;
    }

    public function getTahunAkhirAttribute()
    {
        $tahun = $this->kasProjects->last() ? Carbon::parse($this->kasProjects->last()->create_at)->format('Y') : date('Y');
        return $tahun;
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

    public function getProfitAkhirAttribute()
    {
        $profit = $this->nilai_tagihan - $this->kasProjects()->where('jenis', 0)->sum('nominal');
        return $profit;
    }

    public function getNfProfitAkhirAttribute()
    {
        return number_format($this->profit_akhir, 0, ',', '.');
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
        // $data['uraian'] = 'Cicilan '.$invoice->project->nama;
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

    //cuma tuhan yang tau ini kenapa bisa berfungsi
    public function pelunasan($invoice_id)
    {
        $db = new KasProject();
        $invoice = InvoiceTagihan::find($invoice_id);

        $data['nominal'] = $invoice->sisa_tagihan;
        $data['uraian'] = 'Pelunasan '.$invoice->project->nama;
        $data['jenis'] = 1;
        $data['project_id'] = $invoice->project_id;

        $sisa = $db->sisaTerakhir($invoice->project_id);
        $pengeluaranTotal = $sisa * -1;
        $uraian = "Pengembalian Modal Invesotor ".$invoice->project->nama;
        $pesan = [];

        DB::beginTransaction();

        try {

            $this->updatePelunasan($invoice, $data);

            $store = $this->masukKasBesar($data);

            $pesanPelunasan = "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                "*Form Pelunasan Project*\n".
                "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                "Project :  *".$store->project->nama."*\n\n".
                "Nilai    :  *Rp. ".number_format($store->nominal, 0, ',', '.')."*\n\n".
                "Ditransfer ke rek:\n\n".
                "Bank      : ".$store->bank."\n".
                "Nama    : ".$store->nama_rek."\n".
                "No. Rek : ".$store->no_rek."\n\n".
                "==========================\n".
                "Sisa Saldo Kas Besar : \n".
                "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
                "Total Modal Investor : \n".
                "Rp. ".number_format($store->modal_investor_terakhir, 0, ',', '.')."\n\n".
                "Total Kas Project : \n".
                "Rp. ".number_format($sisa, 0, ',', '.')."\n\n".
                "Terima kasih 🙏🙏🙏\n";

            // add $pesanPelunasan to $pesan array
            array_push($pesan, $pesanPelunasan);

            $store2 = $this->withdrawPengeluaran($sisa, $invoice->project_id, $uraian);

            $pesanWithdraw =  "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                            "*Form Withdraw Project*\n".
                            "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                            "Project : "."*".$store2->project->nama."*\n\n".
                            "Uraian :  *".$store2->uraian."*\n".
                            "Nilai    :  *Rp. ".number_format($store2->nominal, 0, ',', '.')."*\n\n".
                            "Ditransfer ke rek:\n\n".
                            "Bank      : ".$store2->bank."\n".
                            "Nama    : ".$store2->nama_rek."\n".
                            "No. Rek : ".$store2->no_rek."\n\n".
                            "==========================\n".
                            "Sisa Saldo Kas Besar : \n".
                            "Rp. ".number_format($store2->saldo, 0, ',', '.')."\n\n".
                            "Total Modal Investor : \n".
                            "Rp. ".number_format($store2->modal_investor_terakhir, 0, ',', '.')."\n\n".
                            "Total Kas Project : \n".
                            "Rp. ".number_format($sisa, 0, ',', '.')."\n\n".
                            "Terima kasih 🙏🙏🙏\n";

            // add $pesanWithdraw to $pesan array
            array_push($pesan, $pesanWithdraw);

            // jika ada profit maka bagi deviden
            if ($invoice->profit > 10) {

                $deviden = $this->devidenProject($invoice);

                foreach ($deviden as $d) {
                    $p = "";

                    $store3 = $this->devidenStore($d);

                    $p = "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                        "*Form Deviden Project*\n".
                        "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                        "Uraian  : ".$store3->uraian."\n".
                        "Nilai :  *Rp. ".number_format($store3->nominal, 0, ',', '.')."*\n\n".
                        "Ditransfer ke rek:\n\n".
                        "Bank      : ".$store3->bank."\n".
                        "Nama    : ".$store3->nama_rek."\n".
                        "No. Rek : ".$store3->no_rek."\n\n".
                        "==========================\n".
                        "Sisa Saldo Kas Besar : \n".
                        "Rp. ".number_format($store3->saldo, 0, ',', '.')."\n\n".
                        "Total Modal Investor : \n".
                        "Rp. ".number_format($store3->modal_investor_terakhir, 0, ',', '.')."\n\n".
                        "Terima kasih 🙏🙏🙏\n";

                    array_push($pesan, $p);
                }

            }


            DB::commit();


        } catch (\Throwable $th) {

            DB::rollBack();
            $result = [
                'status' => 0,
                'message' => $th->getMessage()
            ];
            return $result;

        }

        foreach ($pesan as $p) {
            $this->sendWa($p);
            usleep(100000);
        }

        $result = [
            'status' => 1,
            'message' => 'Pelunasan berhasil diproses!'
        ];

        return $result;
    }

    private function updatePelunasan(InvoiceTagihan $invoice, $data)
    {
        $invoice->update([
            'dibayar' => $invoice->dibayar + $data['nominal'],
            'sisa_tagihan' => $invoice->sisa_tagihan - $data['nominal'],
            'finished' => 1
        ]);

        Project::find($invoice->project_id)->update([
            'project_status_id' => 2
        ]);
    }

    private function devidenStore($data)
    {
        $kb = new KasBesar();

        if (!isset($data['project_id'])) {
            throw new \Exception('project_id is not set in $data');
        }

        $store = $kb->create([
            'project_id' => $data['project_id'],
            'nominal' => $data['nominal'],
            'jenis' => $data['jenis'],
            'uraian' => $data['uraian'],
            'no_rek' => $data['no_rek'],
            'nama_rek' => $data['nama_rek'],
            'bank' => $data['bank'],
            'saldo' => $kb->saldoTerakhir() - $data['nominal'],
            'modal_investor_terakhir' => $kb->modalInvestorTerakhir()
        ]);

        return $store;
    }

    private function masukKasBesar($data)
    {
        $kb = new KasBesar();
        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        if (!isset($data['project_id'])) {
            throw new \Exception('project_id is not set in $data');
        }

        $store = $kb->create([
            'project_id' => $data['project_id'],
            'nominal' => $data['nominal'],
            'jenis' => $data['jenis'],
            'uraian' => $data['uraian'],
            'no_rek' => $rekening->no_rek,
            'nama_rek' => $rekening->nama_rek,
            'bank' => $rekening->bank,
            'saldo' => $kb->saldoTerakhir() + $data['nominal'],
            'modal_investor_terakhir' => $kb->modalInvestorTerakhir()
        ]);

        return $store;

    }

    private function withdrawPengeluaran($sisa, $project_id, $uraian)
    {
        $kb = new KasBesar();
        $rekening = Rekening::where('untuk', 'withdraw')->first();
        if ($sisa < 0) {
            $sisa = $sisa * -1;
        }

        $store = $kb->create([
            'project_id' => $project_id,
            'nominal' => $sisa,
            'jenis' => 0,
            'uraian' => $uraian,
            'no_rek' => $rekening->no_rek,
            'nama_rek' => $rekening->nama_rek,
            'bank' => $rekening->bank,
            'saldo' => $kb->saldoTerakhir() - $sisa,
            'modal_investor' => $sisa,
            'modal_investor_terakhir' => $kb->modalInvestorTerakhir() + $sisa
        ]);

        return $store;

    }

    private function sendWa($pesan)
    {
        $group = GroupWa::where('untuk', 'kas-besar')->first();
        $send = new StarSender($group->nama_group, $pesan);
        $res = $send->sendGroup();

        $status = ($res == 'true') ? 1 : 0;

        PesanWa::create([
            'pesan' => $pesan,
            'tujuan' => $group->nama_group,
            'status' => $status,
        ]);
    }

    private function devidenProject(InvoiceTagihan $invoice)
    {
        $profit = $invoice->profit;

        $investor = Investor::all();
        $data = [];

        foreach ($investor as $i) {
            $data[] = [
                'no_rek' => $i->no_rek,
                'bank' => $i->bank,
                'nama_rek' => $i->nama_rek,
                'jenis' => 0,
                'nominal' => $profit * $i->persentase / 100,
                'uraian' => 'Bagi Deviden '.$i->nama,
                'project_id' => $invoice->project_id
            ];
        }
        // make every nominal to exact same as profit
        $total = 0;
        foreach ($data as $d) {
            $total += $d['nominal'];
        }

        if($total > $profit) {
            $selisih = $total - $profit;
            $data[0]['nominal'] -= $selisih;
        } else if($total < $profit) {
            $selisih = $profit - $total;
            $data[0]['nominal'] += $selisih;
        }

        return $data;

    }

}
