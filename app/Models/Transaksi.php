<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getIdTanggalAttribute()
    {
        return date('d-m-Y', strtotime($this->attributes['tanggal']));
    }

    public function getNfBeratAttribute()
    {
        return number_format($this->attributes['berat'], 0, ',', '.');
    }

    public function getNfHargaAttribute()
    {
        return number_format($this->harga, 0, ',', '.');
    }

    public function getNfTotalAttribute()
    {
        return number_format($this->attributes['total'], 0, ',', '.');
    }

//     public function getTotalAttribute($value)
// {
//     return floatval(str_replace(',', '', $value));
// }

    public function getNfProfitAttribute()
    {
        return number_format($this->profit, 0, ',', '.');
    }

    public function getNfPPHAttribute()
    {
        return number_format($this->attributes['pph'], 0, ',', '.');
    }

    // public function getPPHAttribute($value)
    // {
    //     return number_format($value, 0, ',', '.');
    // }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function notaTagihan($customer_id)
    {
        return $this->where('customer_id', $customer_id)->where('status', 1)->where('tagihan', 0)->orderBy('nota_timbangan')->get();
    }

    public function notaBayar($supplier_id)
    {
        return $this->where('supplier_id', $supplier_id)->where('status', 1)->where('bayar', 0)->orderBy('nota_timbangan')->get();
    }

    public function notaInvoice($customer_id, $month, $year)
    {
        return $this->whereMonth('tanggal', $month)->whereYear('tanggal', $year)
                    ->where('customer_id', $customer_id)
                    ->where('status', 1)->where('tagihan', 1)->where('ppn', 0)
                    ->orderBy('nota_timbangan')->get();
    }

    public function formTransaksi($customer_id)
    {
        return $this->where('customer_id', $customer_id)->where('status', 0)->orderBy('nota_timbangan')->get();
    }

    public function dataTahun()
    {
        return $this->selectRaw('YEAR(tanggal) tahun')->groupBy('tahun')->get();
    }

    public function rekapInvoice($customer_id, $month, $year)
    {
        return $this->where('customer_id', $customer_id)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)
                    ->where('status', 1)->where('tagihan', 1)->where('ppn', 1)
                    ->orderBy('nota_timbangan')->get();
    }

    public function formTransaksiBerat($customer_id)
    {
        $transaksi = $this->where('customer_id', $customer_id)->where('status', 0)->sum('berat');
        return $transaksi == 0 ? 0 : number_format($transaksi, 0, ',', '.');
    }

    public function formTransaksiTotal($customer_id)
    {
        $transaksi = $this->where('customer_id', $customer_id)->where('status', 0)->sum('total');
        return $transaksi == 0 ? 0 : number_format($transaksi, 0, ',', '.');
    }

    public function notaTransaksiBerat($customer_id)
    {
        $transaksi = $this->where('customer_id', $customer_id)->where('status', 1)->where('tagihan', 0)->sum('berat');
        return $transaksi == 0 ? 0 : number_format($transaksi, 0, ',', '.');
    }

    public function notaBayarBerat($supplier_id)
    {
        $transaksi = $this->where('supplier_id', $supplier_id)->where('status', 1)->where('bayar', 0)->sum('berat');
        return $transaksi == 0 ? 0 : number_format($transaksi, 0, ',', '.');
    }

    public function notaTransaksiTotal($customer_id)
    {
        $transaksi = $this->where('customer_id', $customer_id)->where('status', 1)->where('tagihan', 0)->sum('total');
        return $transaksi == 0 ? 0 : number_format($transaksi, 0, ',', '.');
    }

    public function notaBayarTotal($supplier_id)
    {
        $transaksi = $this->where('supplier_id', $supplier_id)->where('status', 1)->where('bayar', 0)->sum('total');
        return $transaksi == 0 ? 0 : number_format($transaksi, 0, ',', '.');
    }

    public function notaTransaksiTotalTagihan($customer_id)
    {
        $transaksi = $this->where('customer_id', $customer_id)->where('status', 1)->where('tagihan', 0)->sum('total_tagihan');
        return $transaksi == 0 ? 0 : $transaksi;
    }

    public function notaTransaksiTotalBayar($customer_id)
    {
        $transaksi = $this->where('customer_id', $customer_id)->where('status', 1)->where('tagihan', 0)->sum('total_bayar');
        return $transaksi == 0 ? 0 : $transaksi;
    }

    public function notaTagihanTotalProfit($customer_id)
    {
        $transaksi = $this->where('customer_id', $customer_id)->where('status', 1)->where('tagihan', 0)->sum('profit');
        return $transaksi == 0 ? 0 : number_format($transaksi, 0, ',', '.');
    }

    public function notaTagihanTotalPPH($customer_id)
    {
        $transaksi = $this->where('customer_id', $customer_id)->where('status', 1)->where('tagihan', 0)->sum('pph');
        return $transaksi == 0 ? 0 : number_format($transaksi, 0, ',', '.');
    }

    public function notaBayarTotalBayar($supplier_id)
    {
        $transaksi = $this->where('supplier_id', $supplier_id)->where('status', 1)->where('bayar', 0)->sum('total_bayar');
        return $transaksi == 0 ? 0 : $transaksi;
    }

    public function totalNotaTagihan()
    {
        return $this->where('status', 1)->where('tagihan', 0)->count();
    }

    public function totalNotaBayar()
    {
        return $this->where('status', 1)->where('bayar', 0)->count();
    }

    public function totalInvoicePpn()
    {
        return $this->where('status', 1)->where('tagihan', 1)->where('ppn', 0)->count();
    }

    // sum total tagihan group by customer that has transaksis
    public function totalTagihan()
    {
        return $this->selectRaw('customer_id, sum(total_tagihan) as total_tagihan')
                    ->where('status', 1)->where('tagihan', 0)
                    ->groupBy('customer_id')->get();
    }

    public function profitPerBulan($month, $year)
    {
        return $this->whereMonth('tanggal', $month)->whereYear('tanggal', $year)
                    ->where('status', 1)
                    ->sum('profit');
    }

    public function statistikBulanan($customer_id, $month, $year)
    {
        return $this->where('customer_id', $customer_id)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)
                    ->where('status', 1)->get();
    }

    public function statistikTahunan($customer_id, $year)
    {
        return $this->where('customer_id', $customer_id)->whereYear('tanggal', $year)
                    ->where('status', 1)->get();
    }

}
