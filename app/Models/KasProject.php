<?php

namespace App\Models;

use App\Services\StarSender;
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

    public function transaksiKeluarPpn($data)
    {
        $data['jenis'] = 0;

        DB::beginTransaction();

        $ppn = $data['nominal'] * 0.11;
        $total = $data['nominal'] + $ppn;

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

        $this->create([
            'project_id' => $data['project_id'],
            'nominal' => $ppn,
            'jenis' => $data['jenis'],
            'sisa' => $this->sisaTerakhir($data['project_id']) - $ppn,
            'uraian' => 'PPn ' . $data['uraian'],
            'no_rek' => $data['no_rek'],
            'nama_rek' => $data['nama_rek'],
            'bank' => $data['bank'],
            'ppn_masuk' => 1,
        ]);

        $db = new KasBesar();

        $data['saldo'] = $db->saldoTerakhir() - $data['nominal'];
        $data['modal_investor_terakhir'] = $db->modalInvestorTerakhir();

        $store = $db->create([
            'nominal' => $data['nominal'],
            'jenis' => $data['jenis'],
            'saldo' => $data['saldo'],
            'modal_investor_terakhir' => $data['modal_investor_terakhir'],
            'uraian' => $data['uraian'],
            'no_rek' => $data['no_rek'],
            'nama_rek' => $data['nama_rek'],
            'bank' => $data['bank'],
            'project_id' => $data['project_id']
        ]);

        $data['saldo'] = $db->saldoTerakhir() - $ppn;

        $store = $db->create([
            'nominal' => $ppn,
            'jenis' => $data['jenis'],
            'saldo' => $data['saldo'],
            'modal_investor_terakhir' => $data['modal_investor_terakhir'],
            'uraian' => 'PPn ' . $data['uraian'],
            'no_rek' => $data['no_rek'],
            'nama_rek' => $data['nama_rek'],
            'bank' => $data['bank'],
            'project_id' => $data['project_id']
        ]);

        DB::commit();

        $inv = InvoiceTagihan::where('project_id', $store->project_id)->first();
        $nilai = $inv->nilai_tagihan;
        $profit = $inv->profit;
        $sisa = $this->sisaTerakhir($store->project_id);
        $ppnMasukan = $inv->ppn_masukan;

        $group = GroupWa::where('untuk', 'kas-besar')->first()->nama_group;

        $pesan =    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n".
                    "*Form Transaksi (Dana Keluar)*\n".
                    "🔴🔴🔴🔴🔴🔴🔴🔴🔴\n\n".
                    "Customer : ".$store->project->customer->singkatan."\n".
                    "Project : "."*".$store->project->nama."*\n".
                    "Uraian :  *".$data['uraian']."*\n\n".
                    "Nilai    :  *Rp. ".number_format($total, 0, ',', '.')."*\n\n".
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
                    "Total PPn Masukan : \n".
                    "Rp. ".number_format($ppnMasukan, 0, ',', '.')."\n\n".
                    "Nilai Project : \n".
                    "Rp. ".number_format($nilai, 0, ',', '.')."\n\n".
                    "Estimasi Profit Sementara : \n".
                    "Rp. ".number_format($profit, 0, ',', '.')."\n\n".
                    "Terima kasih 🙏🙏🙏\n";

        $this->sendWa($group, $pesan);

        $result = [
            'status' => 'success',
            'message' => 'Transaksi berhasil ditambahkan'
        ];

        return $result;

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
                    'void' => 1,
                ]);

        $db = new KasBesar();

        $data['saldo'] = $db->saldoTerakhir() + $data['nominal'];
        $data['modal_investor_terakhir'] = $db->modalInvestorTerakhir();

        $store = $db->create($data);

        DB::commit();

        return $store;

    }

    public function claim_ppn(KasProject $kasProject)
    {
        $db = new KasBesar();
        $kp = new KasProject();
        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        DB::beginTransaction();

        try {

           $store = $db->create([
                'project_id' => $kasProject->project_id,
                'nominal' => $kasProject->nominal,
                'jenis' => 1,
                'saldo' => $db->saldoTerakhir() + $kasProject->nominal,
                'modal_investor_terakhir' => $db->modalInvestorTerakhir(),
                'uraian' => "Klaim ". $kasProject->uraian,
                'no_rek' => $rekening->no_rek,
                'nama_rek' => $rekening->nama_rek,
                'bank' => $rekening->bank,
            ]);

            $sisaTerakhir = $kp->sisaTerakhir($kasProject->project_id);

            KasProject::create([
                'project_id' => $kasProject->project_id,
                'nominal' => $kasProject->nominal,
                'jenis' => 1,
                'sisa' => $sisaTerakhir + $kasProject->nominal,
                'uraian' => "Klaim ". $kasProject->uraian,
                'no_rek' => $rekening->no_rek,
                'nama_rek' => $rekening->nama_rek,
                'bank' => $rekening->bank,
            ]);

            $kasProject->update([
                'ppn_masuk' => 0,
            ]);

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollback();

            $result = [
                'status' => 'error',
                'message' => 'Gagal mengklaim PPN Masukan'
            ];

            return $result;
        }

        $sisaTerakhir = $kp->sisaTerakhir($kasProject->project_id);

        $group = GroupWa::where('untuk', 'kas-besar')->first()->nama_group;

        $inv = InvoiceTagihan::where('project_id', $kasProject->project_id)->first();
        $nilai = $inv->nilai_tagihan;
        $profit = $inv->profit;
        $ppnMasukan = $inv->ppn_masukan;
        $sisa = $kp->sisaTerakhir($kasProject->project_id);

        if ($inv->finished == 1) {
            $pesan =    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                        "*PPn Masukan Susulan*\n".
                        "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                        "Customer : ".$kasProject->project->customer->singkatan."\n".
                        "Project : "."*".$kasProject->project->nama."*\n".
                        "Uraian :  *Klaim ".$kasProject->uraian."*\n\n".
                        "Nilai    :  *Rp. ".number_format($kasProject->nominal, 0, ',', '.')."*\n\n".
                        "Ditransfer ke rek:\n\n".
                        "Bank      : ".$rekening->bank."\n".
                        "Nama    : ".$rekening->nama_rek."\n".
                        "No. Rek : ".$rekening->no_rek."\n\n".
                        "==========================\n".
                        "Sisa Saldo Kas Besar : \n".
                        "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
                        "Total Modal Investor : \n".
                        "Rp. ".number_format($store->modal_investor_terakhir, 0, ',', '.')."\n\n".
                        "Total PPn Masukan : \n".
                        "Rp. ".number_format($ppnMasukan, 0, ',', '.')."\n\n".
                        "Terima kasih 🙏🙏🙏\n";

        } else {
            $pesan =    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                        "*Klaim PPn Masukan*\n".
                        "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                        "Customer : ".$kasProject->project->customer->singkatan."\n".
                        "Project : "."*".$kasProject->project->nama."*\n".
                        "Uraian :  *Klaim ".$kasProject->uraian."*\n\n".
                        "Nilai    :  *Rp. ".number_format($kasProject->nominal, 0, ',', '.')."*\n\n".
                        "Ditransfer ke rek:\n\n".
                        "Bank      : ".$rekening->bank."\n".
                        "Nama    : ".$rekening->nama_rek."\n".
                        "No. Rek : ".$rekening->no_rek."\n\n".
                        "==========================\n".
                        "Sisa Saldo Kas Besar : \n".
                        "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
                        "Total Modal Investor : \n".
                        "Rp. ".number_format($store->modal_investor_terakhir, 0, ',', '.')."\n\n".
                        "Total Kas Project : \n".
                        "Rp. ".number_format($sisa, 0, ',', '.')."\n\n".
                        "Total PPn Masukan : \n".
                        "Rp. ".number_format($ppnMasukan, 0, ',', '.')."\n\n".
                        "Nilai Project : \n".
                        "Rp. ".number_format($nilai, 0, ',', '.')."\n\n".
                        "Estimasi Profit Sementara : \n".
                        "Rp. ".number_format($profit, 0, ',', '.')."\n\n".
                        "Terima kasih 🙏🙏🙏\n";

        }


        $this->sendWa($group, $pesan);

        $result = [
            'status' => 'success',
            'message' => 'PPN Masukan berhasil diklaim'
        ];

        return $result;

    }

    public function void_transaksi(KasProject $kasProject)
    {
        $db = new KasBesar();
        $kp = new KasProject();
        $rekening = Rekening::where('untuk', 'kas-besar')->first();

        DB::beginTransaction();

        try {


                $store = $db->create([
                    'project_id' => $kasProject->project_id,
                    'nominal' => $kasProject->nominal,
                    'jenis' => 1,
                    'saldo' => $db->saldoTerakhir() + $kasProject->nominal,
                    'modal_investor_terakhir' => $db->modalInvestorTerakhir(),
                    'uraian' => "Void ". $kasProject->uraian,
                    'no_rek' => $kasProject->no_rek,
                    'nama_rek' => $kasProject->nama_rek,
                    'bank' => $kasProject->bank,
                ]);

                $kp->create([
                    'project_id' => $kasProject->project_id,
                    'nominal' => $kasProject->nominal,
                    'jenis' => 1,
                    'sisa' => $kp->sisaTerakhir($kasProject->project_id) + $kasProject->nominal,
                    'uraian' => "Void ". $kasProject->uraian,
                    'no_rek' => $rekening->no_rek,
                    'nama_rek' => $rekening->nama_rek,
                    'bank' => $rekening->bank,
                    'void' => 1
                ]);


                $kasProject->update([
                    'ppn_masuk' => 0,
                    'void' => 1
                ]);




                DB::commit();

            } catch (\Throwable $th) {
                DB::rollback();

                $result = [
                    'status' => 'error',
                    'message' => 'Gagal membatalkan transaksi'
                ];

                return $result;
            }

            $group = GroupWa::where('untuk', 'kas-besar')->first()->nama_group;
            $inv = InvoiceTagihan::where('project_id', $kasProject->project_id)->first();
            $nilai = $inv->nilai_tagihan;
            $profit = $inv->profit;
            $ppnMasukan = $inv->ppn_masukan;
            $sisa = $kp->sisaTerakhir($kasProject->project_id);


            $pesan =    "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n".
                        "*Void Transaksi*\n".
                        "🔵🔵🔵🔵🔵🔵🔵🔵🔵\n\n".
                        "Customer : ".$kasProject->project->customer->singkatan."\n".
                        "Project : "."*".$kasProject->project->nama."*\n".
                        "Uraian :  *Void ".$kasProject->uraian."*\n\n".
                        "Nilai    :  *Rp. ".number_format($kasProject->nominal, 0, ',', '.')."*\n\n".
                        "Ditransfer ke rek:\n\n".
                        "Bank      : ".$kasProject->bank."\n".
                        "Nama    : ".$kasProject->nama_rek."\n".
                        "No. Rek : ".$kasProject->no_rek."\n\n".
                        "==========================\n".
                        "Sisa Saldo Kas Besar : \n".
                        "Rp. ".number_format($store->saldo, 0, ',', '.')."\n\n".
                        "Total Modal Investor : \n".
                        "Rp. ".number_format($store->modal_investor_terakhir, 0, ',', '.')."\n\n".
                        "Total Kas Project : \n".
                        "Rp. ".number_format($sisa, 0, ',', '.')."\n\n".
                        "Total PPn Masukan : \n".
                        "Rp. ".number_format($ppnMasukan, 0, ',', '.')."\n\n".
                        "Nilai Project : \n".
                        "Rp. ".number_format($nilai, 0, ',', '.')."\n\n".
                        "Estimasi Profit Sementara : \n".
                        "Rp. ".number_format($profit, 0, ',', '.')."\n\n".
                        "Terima kasih 🙏🙏🙏\n";

            $this->sendWa($group, $pesan);

            $result = [
                'status' => 'success',
                'message' => 'Transaksi berhasil dibatalkan'
            ];

            return $result;

    }

    private function sendWa($tujuan, $pesan)
    {
        $send = new StarSender($tujuan, $pesan);
        $res = $send->sendGroup();

        $status = ($res == 'true') ? 1 : 0;

        PesanWa::create([
            'pesan' => $pesan,
            'tujuan' => $tujuan,
            'status' => $status,
        ]);
    }

}
