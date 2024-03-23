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
                            'bulan_akhir', 'tahun_akhir', 'balance', 'nf_balance', 'id_estimasi_pembayaran', 'nf_nilai_ppn', 'nf_nilai_pph',
                            'ppn_masukan', 'nf_ppn_masukan', 'total_tagihan', 'nf_total_tagihan'];

    public function getNfNilaiPphAttribute()
    {
        return number_format($this->nilai_pph, 0, ',', '.');
    }

    public function getNfNilaiPpnAttribute()
    {
        return number_format($this->nilai_ppn, 0, ',', '.');
    }

    public function getIdEstimasiPembayaranAttribute()
    {
        return Carbon::parse($this->estimasi_pembayaran)->format('d-m-Y');
    }
    public function kasProjects()
    {
        return $this->hasManyThrough(KasProject::class, Project::class, 'id', 'project_id', 'project_id', 'id');
    }

    public function invoiceTagihanDetails()
    {
        return $this->hasMany(InvoiceTagihanDetail::class);
    }

    public function getBalanceAttribute()
    {
        // sum all nominal from invoiceTagihanDetails
        $total = $this->invoiceTagihanDetails->sum('nominal');
        return $total;
    }

    public function getNfBalanceAttribute()
    {
        return number_format($this->balance, 0, ',', '.');
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

    public function getPpnMasukanAttribute()
    {
        $ppn = $this->kasProjects->where('ppn_masuk', 1)->sum('nominal');
        return $ppn;
    }

    public function getNfPpnMasukanAttribute()
    {
        return number_format($this->ppn_masukan, 0, ',', '.');
    }

    public function getTotalTagihanAttribute()
    {
        $total = $this->nilai_tagihan + $this->nilai_ppn - $this->nilai_pph;
        return $total;
    }

    public function getNfTotalTagihanAttribute()
    {
        return number_format($this->total_tagihan, 0, ',', '.');
    }

    public function getProfitAkhirAttribute()
    {
        $profit = ($this->nilai_tagihan - $this->nilai_pph) - $this->kasProjects()->where('jenis', 0)->sum('nominal');
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

    public static function cutoff(InvoiceTagihan $invoice, $data)
    {
        $data['estimasi_pembayaran'] = Carbon::parse($data['estimasi_pembayaran'])->format('Y-m-d');

        DB::beginTransaction();

        try {
            $invoice->update([
                'cutoff' => 1,
                'estimasi_pembayaran' => $data['estimasi_pembayaran']
            ]);

            $invoice->project->update([
                'project_status_id' => 3
            ]);

            DB::commit();

            $result = [
                'status' => 'success',
                'message' => 'Cutoff berhasil diproses!'
            ];

            return $result;

        } catch (\Throwable $th) {

                DB::rollBack();

                $result = [
                    'status' => 'error',
                    'message' => $th->getMessage()
                ];

                return $result;
        }

    }

    public function cicilan($invoice_id, $data)
    {

        $kb = new KasBesar();
        $invoice = InvoiceTagihan::find($invoice_id);

        $rekening = Rekening::where('untuk', 'kas-besar')->first();
        // $data['uraian'] = 'Cicilan '.$invoice->project->nama;
        $data['nominal'] = str_replace('.', '', $data['nominal']);
        $data['bank'] = $rekening->bank;
        $data['no_rek'] = $rekening->no_rek;
        $data['nama_rek'] = $rekening->nama_rek;
        $data['jenis'] = 1;

        DB::beginTransaction();

        $invoice->update([
                            'dibayar' => $invoice->dibayar + $data['nominal'],
                            'sisa_tagihan' => $invoice->sisa_tagihan - $data['nominal']
                        ]);

        $invoice->invoiceTagihanDetails()->create([
            'uraian' => $data['uraian'],
            'nominal' => $data['nominal']
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
                "Customer : *".$store->project->customer->singkatan."*\n".
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
                "Sisa PPn Masukan : \n".
                "Rp. ".number_format($invoice->ppn_masukan, 0, ',', '.')."\n\n".
                "Profit Project : \n".
                "Rp. ".number_format($invoice->profit, 0, ',', '.')."\n\n".
                "Terima kasih 🙏🙏🙏\n";

            // add $pesanPelunasan to $pesan array
            array_push($pesan, $pesanPelunasan);

            //pengembalian rugi modal

            if ($invoice->profit < 0) {

                $rugi = $this->bagiRugi($invoice);

                foreach ($rugi as $r) {
                    $pesanRugi = "";

                    $store2 = $this->bagiRugiStore($r);

                    $pesanRugi =  "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                        "*Form Bagi Rugi*\n".
                        "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
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

                    // add $pesanRugi to $pesan array
                    array_push($pesan, $pesanRugi);
                }

            }

            // withdraw pengeluaran project
            // $withdraw = $this->withdrawPelunasan($sisa, $invoice->project_id);

            // foreach ($withdraw as $w) {

            //     $pesanWithdraw = '';

            //     $store2 = $this->withdrawPelunasanStore($w);

            //     $pesanWithdraw =    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
            //                         "*Form Withdraw Project*\n".
            //                         "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
            //                         "Project : "."*".$store2->project->nama."*\n\n".
            //                         "Uraian :  *".$store2->uraian."*\n".
            //                         "Nilai    :  *Rp. ".number_format($store2->nominal, 0, ',', '.')."*\n\n".
            //                         "Ditransfer ke rek:\n\n".
            //                         "Bank      : ".$store2->bank."\n".
            //                         "Nama    : ".$store2->nama_rek."\n".
            //                         "No. Rek : ".$store2->no_rek."\n\n".
            //                         "==========================\n".
            //                         "Sisa Saldo Kas Besar : \n".
            //                         "Rp. ".number_format($store2->saldo, 0, ',', '.')."\n\n".
            //                         "Total Modal Investor : \n".
            //                         "Rp. ".number_format($store2->modal_investor_terakhir, 0, ',', '.')."\n\n".
            //                         "Total Kas Project : \n".
            //                         "Rp. ".number_format($sisa, 0, ',', '.')."\n\n".
            //                         "Terima kasih 🙏🙏🙏\n";

            //     array_push($pesan, $pesanWithdraw);

            // }

            // jika ada profit maka bagi deviden
            if ($invoice->profit > 0) {

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

        $invoice->invoiceTagihanDetails()->create([
            'uraian' => $data['uraian'],
            'nominal' => $data['nominal']
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
            'modal_investor_terakhir' => $kb->modalInvestorTerakhir(),
            'investor_modal_id' => $data['investor_modal_id']
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

    private function withdrawPelunasan($sisa,$project_id)
    {
        if($sisa < 0) {
            $sisa = $sisa * -1;
        }

        $investor = InvestorModal::all();
        $data = [];

        foreach ($investor as $i) {
            $data[] = [
                'no_rek' => $i->no_rek,
                'bank' => $i->bank,
                'nama_rek' => $i->nama_rek,
                'jenis' => 0,
                'nominal' => $sisa * $i->persentase / 100,
                'uraian' => 'Withdraw '.$i->nama,
                'project_id' => $project_id,
                'investor_modal_id' => $i->id
            ];
        }
        // make every nominal to exact same as profit
        $total = 0;
        foreach ($data as $d) {
            $total += $d['nominal'];
        }

        if($total > $sisa) {
            $selisih = $total - $sisa;
            $data[0]['nominal'] -= $selisih;
        } else if($total < $sisa) {
            $selisih = $sisa - $total;
            $data[0]['nominal'] += $selisih;
        }

        return $data;
    }

    private function withdrawPelunasanStore($data)
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
            'modal_investor_terakhir' => $kb->modalInvestorTerakhir() + $data['nominal'],
            'investor_modal_id' => $data['investor_modal_id']
        ]);

        $kb->kurangModal($data['nominal'], $data['investor_modal_id']);

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
        $rp = Rekening::where('untuk', 'pengelola')->first();
        $investorModal = InvestorModal::all();
        $profit = $invoice->profit;
        $data = [];
        $investor = Investor::all();
        $totalProfitInvestor = 0;
        $totalProfitPengelola = 0;

        foreach ($investor as $i) {
            $nominal = $profit * $i->persentase / 100;

            if ($i->nama == 'pengelola') {
                $data[] = [
                    'no_rek' => $rp->no_rek,
                    'bank' => $rp->bank,
                    'nama_rek' => $rp->nama_rek,
                    'jenis' => 0,
                    'nominal' => $nominal,
                    'uraian' => 'Bagi Deviden '.$rp->untuk,
                    'project_id' => $invoice->project_id,
                    'investor_modal_id' => null,
                ];
            }

            if ($i->nama == 'investor') {
                $totalProfitInvestor = $nominal;
                foreach ($investorModal as $im) {
                    if ($im->persentase > 0) {
                        $data[] = [
                            'no_rek' => $im->no_rek,
                            'bank' => $im->bank,
                            'nama_rek' => $im->nama_rek,
                            'jenis' => 0,
                            'nominal' => $nominal * $im->persentase / 100,
                            'uraian' => 'Bagi Deviden '.$im->nama,
                            'project_id' => $invoice->project_id,
                            'investor_modal_id' => $im->id
                        ];
                    }
                }
            }
        }

        $total = array_sum(array_column($data, 'nominal'));
        if ($total > $profit) {
            $data[0]['nominal'] -= $total - $profit;
        } elseif ($total < $profit) {
            $data[0]['nominal'] += $profit - $total;
        }

        return $data;

    }

    private function bagiRugi(InvoiceTagihan $invoice)
    {
        $rp = Rekening::where('untuk', 'pengelola')->first();
        $investorModal = InvestorModal::all();
        $profit = $invoice->profit * -1;
        $data = [];
        $investor = Investor::all();
        $totalProfitInvestor = 0;
        $totalProfitPengelola = 0;

        foreach ($investor as $i) {
            $nominal = $profit * $i->persentase / 100;

            if ($i->nama == 'pengelola') {
                $data[] = [
                    'no_rek' => $rp->no_rek,
                    'bank' => $rp->bank,
                    'nama_rek' => $rp->nama_rek,
                    'jenis' => 1,
                    'nominal' => $nominal,
                    'uraian' => 'Bagi Rugi '.$rp->untuk,
                    'project_id' => $invoice->project_id,
                    'investor_modal_id' => null,
                ];
            }

            if ($i->nama == 'investor') {
                $totalProfitInvestor = $nominal;
                foreach ($investorModal as $im) {
                    if ($im->persentase > 0) {
                        $data[] = [
                            'no_rek' => $im->no_rek,
                            'bank' => $im->bank,
                            'nama_rek' => $im->nama_rek,
                            'jenis' => 1,
                            'nominal' => $nominal * $im->persentase / 100,
                            'uraian' => 'Bagi Rugi '.$im->nama,
                            'project_id' => $invoice->project_id,
                            'investor_modal_id' => $im->id
                        ];
                    }
                }
            }
        }

        $total = array_sum(array_column($data, 'nominal'));
        if ($total > $profit) {
            $data[0]['nominal'] -= $total - $profit;
        } elseif ($total < $profit) {
            $data[0]['nominal'] += $profit - $total;
        }

        return $data;
    }

    private function bagiRugiStore($data)
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
            'saldo' => $kb->saldoTerakhir() + $data['nominal'],
            'modal_investor_terakhir' => $kb->modalInvestorTerakhir(),
            'investor_modal_id' => $data['investor_modal_id']
        ]);

        return $store;
    }

    public function invoice_ppn_bayar(InvoiceTagihan $invoice)
    {
        $db = new KasBesar();

        DB::beginTransaction();

        try {
            $store = $db->create([
                'project_id' => $invoice->project_id,
                'nominal' => $invoice->nilai_ppn,
                'jenis' => 0,
                'uraian' => 'Pembayaran PPN '.$invoice->project->nama,
                'no_rek' => '-',
                'nama_rek' => 'Negara',
                'bank' => 'Negara',
                'saldo' => $db->saldoTerakhir() - $invoice->nilai_ppn,
                'modal_investor_terakhir' => $db->modalInvestorTerakhir()
            ]);

            $invoice->update([
                'ppn' => 1
            ]);

            DB::commit();

            $pesan =    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                        "*Form Pembayaran PPN*\n".
                        "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                        "Customer :  *".$store->project->customer->nama."*\n".
                        "Project :  *".$store->project->nama."*\n\n".
                        "Nilai    :  *Rp. ".number_format($store->nominal, 0, ',', '.')."*\n\n".
                        "Ditransfer ke rek:\n\n".
                        "Bank      : ".$store->bank."\n".
                        "Nama     : ".$store->nama_rek."\n".
                        "No. Rek  : ".$store->no_rek."\n\n".
                        "==========================\n".
                        "Sisa Saldo Kas Besar : \n".
                        "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
                        "Total Modal Investor : \n".
                        "Rp. ".number_format($store->modal_investor_terakhir, 0, ',', '.')."\n\n".
                        "Terima kasih 🙏🙏🙏\n";

            $result = [
                'status' => 'success',
                'message' => 'Pembayaran PPN berhasil diproses!'
            ];


        } catch (\Throwable $th) {

            DB::rollBack();

            $result = [
                'status' => 'error',
                'message' => $th->getMessage()
            ];

        }

        $this->sendWa($pesan);

        return $result;

    }

}
