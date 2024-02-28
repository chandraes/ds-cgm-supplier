@extends('layouts.doc-nologo-1')
@section('content')
<div class="container-fluid">
    <center>
        <h2>REKAP KAS BESAR</h2>
        <h2>{{$stringBulanNow}} {{$tahun}}</h2>
    </center>
</div>
<div class="container-fluid table-responsive ml-3 text-pdf">
    <div class="row mt-3">
        <table class="table table-hover table-bordered text-pdf table-pdf" id="rekapTable">
            <thead class=" table-success">
            <tr>
                <th class="text-center align-middle text-pdf table-pdf">Tanggal</th>
                <th class="text-center align-middle text-pdf table-pdf">Uraian</th>
                <th class="text-center align-middle text-pdf table-pdf">Deposit</th>
                <th class="text-center align-middle text-pdf table-pdf">Titipan</th>
                <th class="text-center align-middle text-pdf table-pdf">Tagihan</th>
                <th class="text-center align-middle text-pdf table-pdf">Bayar</th>
                <th class="text-center align-middle text-pdf table-pdf">Masuk</th>
                <th class="text-center align-middle text-pdf table-pdf">Keluar</th>
                <th class="text-center align-middle text-pdf table-pdf">Saldo</th>
                <th class="text-center align-middle text-pdf table-pdf">Transfer Ke Rekening</th>
                <th class="text-center align-middle text-pdf table-pdf">Bank</th>
                <th class="text-center align-middle text-pdf table-pdf">Modal Investor</th>
            </tr>
            <tr class="table-warning">
                <td colspan="7" class="text-center align-middle text-pdf table-pdf">Saldo Bulan
                    {{$stringBulan}} {{$tahunSebelumnya}}</td>
                <td class="text-pdf table-pdf"></td>
                <td class="text-center align-middle text-pdf table-pdf">Rp. {{$dataSebelumnya ? number_format($dataSebelumnya->saldo,
                    0, ',','.') : ''}}</td>
                <td class="text-pdf table-pdf"></td>
                <td class="text-pdf table-pdf"></td>
                <td class="text-center align-middle text-pdf table-pdf">Rp. {{$dataSebelumnya ?
                    number_format($dataSebelumnya->modal_investor_terakhir, 0,',','.') : ''}}</td>
            </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                <tr>
                    <td class="text-center align-middle text-pdf table-pdf">{{$d->id_tanggal}}</td>
                    <td class="text-center align-middle text-pdf table-pdf">
                        @if ($d->invoice_tagihan_id)
                        <a href="{{route('rekap.kas-besar.detail-tagihan', ['invoice' => $d->invoice_tagihan_id])}}">{{$d->uraian}}</a>
                        @elseif($d->invoice_bayar_id)
                        <a href="{{route('rekap.kas-besar.detail-bayar', ['invoice' => $d->invoice_bayar_id])}}">{{$d->uraian}}</a>
                        @else
                        {{$d->uraian}}
                        @endif

                    </td>
                    <td class="text-center align-middle text-pdf table-pdf">
                        {{$d->nomor_deposit != 00 ? $d->kode_deposit.$d->nomor_deposit : ''}}
                    </td>
                    <td class="text-center align-middle text-pdf table-pdf">
                        {{$d->nomor_titipan != 00 ? $d->kode_titipan.$d->nomor_titipan : ''}}
                    </td>
                    <td class="text-center align-middle text-pdf table-pdf">
                        {{$d->nomor_tagihan != 00 ? $d->kode_tagihan.$d->nomor_tagihan : ''}}
                    </td>
                    <td class="text-center align-middle text-pdf table-pdf">
                        {{$d->nomor_bayar != 00 ? $d->kode_bayar.$d->nomor_bayar : ''}}
                    </td>
                    <td class="text-center align-middle text-pdf table-pdf">{{$d->jenis === 1 ?
                        number_format($d->nominal_transaksi, 0, ',', '.') : ''}}
                    </td>
                    <td class="text-center align-middle text-pdf table-pdf">{{$d->jenis === 0 ?
                        number_format($d->nominal_transaksi, 0, ',', '.') : ''}}
                    </td>
                    <td class="text-center align-middle text-pdf table-pdf">{{number_format($d->saldo, 0,',','.')}}</td>
                    <td class="text-center align-middle text-pdf table-pdf">{{$d->nama_rek}}</td>
                    <td class="text-center align-middle text-pdf table-pdf">{{$d->bank}}</td>
                    <td class="text-center align-middle text-pdf table-pdf">{{number_format($d->modal_investor, 0, ',', '.')}}</td>
                </tr>
                @endforeach
                <tr>
                    <td class="text-pdf table-pdf" style="height: 13px"></td>
                    <td class="text-pdf table-pdf"></td>
                    <td class="text-pdf table-pdf"></td>
                    <td class="text-pdf table-pdf"></td>
                    <td class="text-pdf table-pdf"></td>
                    <td class="text-pdf table-pdf"></td>
                    <td class="text-pdf table-pdf"></td>
                    <td class="text-pdf table-pdf"></td>
                    <td class="text-pdf table-pdf"></td>
                    <td class="text-pdf table-pdf"></td>
                    <td class="text-pdf table-pdf"></td>
                    <td class="text-pdf table-pdf"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-center align-middle text-pdf table-pdf" colspan="6"><strong>GRAND TOTAL</strong></td>
                    <td class="text-center align-middle text-pdf table-pdf"><strong>{{number_format($data->where('jenis',
                            1)->sum('nominal_transaksi'), 0, ',', '.')}}</strong></td>
                    <td class="text-center align-middle text-danger"><strong>{{number_format($data->where('jenis',
                            0)->sum('nominal_transaksi'), 0, ',', '.')}}</strong></td>
                    {{-- latest saldo --}}
                    <td class="text-center align-middle text-pdf table-pdf">
                        <strong>
                            {{$data->last() ? number_format($data->last()->saldo, 0, ',', '.') : ''}}
                        </strong>
                    </td>
                    <td class="text-pdf table-pdf"></td>
                    <td class="text-pdf table-pdf"></td>
                    <td class="text-center align-middle text-pdf table-pdf">
                        <strong>
                            {{$data->last() ? number_format($data->last()->modal_investor_terakhir, 0, ',', '.') : ''}}
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
