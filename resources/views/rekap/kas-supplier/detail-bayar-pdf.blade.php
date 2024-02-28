@extends('layouts.doc-nologo-1')
@section('content')
<div class="container-fluid">
    <center>
        <h2>DETAIL BAYAR SUPPLIER</h2>
        <h2>{{$supplier->nama}}</h2>
    </center>
</div>
<div class="container-fluid table-responsive ml-3 text-pdf">
    <div class="row mt-3">
        <div style="padding-left: 0.5rem">
            <h3> Kode Bayar : {{$invoice->format_no_invoice}}</h3>
        </div>

        <table class="table table-bordered table-hover table-pdf text-pdf" id="tableTransaksi">
            <thead class="table-success">
                <tr>
                    <th class="text-center align-middle table-pdf text-pdf">No</th>
                    <th class="text-center align-middle table-pdf text-pdf">Tanggal</th>
                    <th class="text-center align-middle table-pdf text-pdf">Customer</th>
                    <th class="text-center align-middle table-pdf text-pdf">Nota Timbangan</th>
                    <th class="text-center align-middle table-pdf text-pdf">Berat</th>
                    <th class="text-center align-middle table-pdf text-pdf" style="width: 3%">Sat</th>
                    <th class="text-center align-middle table-pdf text-pdf">Harga Satuan</th>
                    <th class="text-center align-middle table-pdf text-pdf">Total Harga</th>
                    <th class="text-center align-middle table-pdf text-pdf">Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                <tr>
                    <td class="text-center align-middle table-pdf text-pdf">{{$loop->iteration}}</td>
                    <td class="text-center align-middle table-pdf text-pdf">{{$d->id_tanggal}}</td>
                    <td class="text-center align-middle table-pdf text-pdf">{{$d->customer->singkatan}}</td>
                    <td class="text-center align-middle table-pdf text-pdf">{{$d->nota_timbangan}}</td>
                    <td class="text-center align-middle table-pdf text-pdf">{{$d->nf_berat}}</td>
                    <td class="text-center align-middle table-pdf text-pdf">Kg</td>
                    <td class="text-center align-middle table-pdf text-pdf">{{$d->nf_harga}}</td>
                    <td class="text-end align-middle table-pdf text-pdf">{{$d->nf_total}}</td>
                    <td class="text-end align-middle table-pdf text-pdf">{{number_format($d->total_bayar,0,',','.')}}</td>

                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-center align-middle table-pdf text-pdf">Grand Total</th>
                    <th class="text-center align-middle table-pdf text-pdf">{{number_format($totalBerat,0,',','.')}}</th>
                    <th class="text-center align-middle table-pdf text-pdf">Kg</th>
                    <th class="text-center align-middle table-pdf text-pdf"></th>
                    <th class="text-end align-middle table-pdf text-pdf">{{number_format($total, 0,',','.')}}</th>
                    <th class="text-end align-middle table-pdf text-pdf">{{number_format($totalTagihan, 0, ',','.')}}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
