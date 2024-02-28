@extends('layouts.doc-nologo-1')
@section('content')
<div class="container-fluid">
    <center>
        <h2>REKAP KAS SUPPLIER</h2>
        <h2>{{$supplier->nama}}</h2>
        <h2>{{$stringBulanNow}} {{$tahun}}</h2>
    </center>
</div>
<div class="container-fluid table-responsive ml-3 text-pdf">
    <div class="row mt-3">
        <table class="table table-hover table-bordered table-pdf text-pdf" id="rekapTable">
            <thead class=" table-success">
            <tr>
                <th class="text-center align-middle table-pdf text-pdf">Tanggal</th>
                <th class="text-center align-middle table-pdf text-pdf">Uraian</th>
                <th class="text-center align-middle table-pdf text-pdf">NO DO</th>
                <th class="text-center align-middle table-pdf text-pdf">Masuk</th>
                <th class="text-center align-middle table-pdf text-pdf">Keluar</th>
                <th class="text-center align-middle table-pdf text-pdf">Saldo</th>
            </tr>
            <tr class="table-warning">
                <td colspan="5" class="text-center align-middle table-pdf text-pdf">Saldo Bulan
                    {{$stringBulan}} {{$tahunSebelumnya}}</td>
                <td class="text-center align-middle table-pdf text-pdf">Rp. {{$dataSebelumnya ? number_format($dataSebelumnya->saldo,
                    0, ',','.') : ''}}</td>
            </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                <tr>
                    <td class="text-center align-middle table-pdf text-pdf">{{$d->id_tanggal}}</td>
                    <td class="text-center align-middle table-pdf text-pdf">{{$d->uraian}}</td>
                    <td class="text-center align-middle table-pdf text-pdf">
                        {{$d->no_do}}
                    </td>
                    <td class="text-center align-middle table-pdf text-pdf">{{$d->jenis === 1 ?
                        number_format($d->nominal_transaksi, 0, ',', '.') : ''}}
                    </td>
                    <td class="text-center align-middle text-danger table-pdf text-pdf">{{$d->jenis === 0 ?
                        number_format($d->nominal_transaksi, 0, ',', '.') : ''}}
                    </td>
                    <td class="text-center align-middle table-pdf text-pdf">{{number_format($d->saldo, 0,',','.')}}</td>
                </tr>
                @endforeach
                <tr>
                    <td class="table-pdf text-pdf" style="height: 16px"></td>
                    <td class="table-pdf text-pdf"></td>
                    <td class="table-pdf text-pdf"></td>
                    <td class="table-pdf text-pdf"></td>
                    <td class="table-pdf text-pdf"></td>
                    <td class="table-pdf text-pdf"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-center align-middle table-pdf text-pdf" colspan="3"><strong>GRAND TOTAL</strong></td>
                    <td class="text-center align-middle table-pdf text-pdf"><strong>{{number_format($data->where('jenis',
                            1)->sum('nominal_transaksi'), 0, ',', '.')}}</strong></td>
                    <td class="text-center align-middle text-danger table-pdf text-pdf"><strong>{{number_format($data->where('jenis',
                            0)->sum('nominal_transaksi'), 0, ',', '.')}}</strong></td>
                    {{-- latest saldo --}}
                    <td class="text-center align-middle table-pdf text-pdf">
                        <strong>
                            {{$data->last() ? number_format($data->last()->saldo, 0, ',', '.') : ''}}
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
