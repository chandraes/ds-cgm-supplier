@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mb-5">
        <div class="col-md-12 text-center">
            <h1><u>Invoice</u></h1>
        </div>
    </div>
    @php
        $profit = 0;
    @endphp
    @if ($errors->any())
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Whoops!</strong> Ada kesalahan dalam input data:
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </ul>
            </div>
        </div>
    </div>
    @endif
    <div class="flex-row justify-content-between mt-3">
        <div class="col-md-6">
            <table class="table">
                <tr class="text-center">
                    <td><a href="{{route('home')}}"><img src="{{asset('images/dashboard.svg')}}" alt="dashboard"
                                width="30"> Dashboard</a></td>
                    <td><a href="{{route('rekap')}}"><img src="{{asset('images/rekap.svg')}}" alt="dokumen"
                                width="30"> REKAP</a></td>

                </tr>
            </table>
        </div>
    </div>
    <div class="row mt-3">
        <table class="table table-bordered table-hover" id="data-table">
            <thead class="table-success">
                <tr>
                    <th class="text-center align-middle">No</th>
                    <th class="text-center align-middle">Customer</th>
                    <th class="text-center align-middle">Project</th>
                    <th class="text-center align-middle">Nilai Kontrak</th>
                    <th class="text-center align-middle">Total Kas Project<br>(Modal)</th>
                    <th class="text-center align-middle">PPh<br>Dipotong</th>
                    <th class="text-center align-middle">PPh<br>Disimpan</th>
                    <th class="text-center align-middle">Profit</th>
                </tr>
            </thead>
            <tbody>
                @php
                $modal = 0;
                $pph = 0;
                $pph_badan = 0;
                @endphp
                @foreach ($data as $d)
                <tr>
                    <td class="text-center align-middle"></td>
                    <td class="text-center align-middle">{{$d->customer->singkatan}}</td>
                    <td class="text-start align-middle">
                        <a href="{{route('rekap.invoice.detail-project', ['project'=>$d->project->id])}}">
                            {{$d->project->nama}}
                        </a>
                    </td>
                    <td class="text-end align-middle">
                        <div class="text-end">
                            <a href="#" data-bs-toggle="modal"
                            data-bs-target="#detailInvoice-{{$d->id}}"> {{$d->nf_nilai_tagihan}}</a>
                        </div>

                        @include('billing.nota-tagihan.detail-modal')

                    </td>
                    <td class="text-end align-middle">
                        {{number_format($d->pengeluaran+$d->nilai_pph, 0, ',', '.')}}
                        @php
                            $modal += ($d->pengeluaran+$d->nilai_pph);
                        @endphp
                    </td>
                    <td class="text-end align-middle">
                        @if ($d->project->pph_badan == 0)
                        {{$d->nf_nilai_pph}}
                        @php
                             $pph += $d->nilai_pph;
                        @endphp
                        @else
                        0
                        @endif
                    </td>
                    <td class="text-end align-middle">
                        @if ($d->project->pph_badan == 1)
                        {{$d->nf_nilai_pph}}
                        @php
                             $pph_badan += $d->nilai_pph;
                        @endphp
                        @else
                        0
                        @endif
                    </td>
                    <td class="text-end align-middle">
                        @php
                            $profit += ($d->nilai_tagihan + $d->pengeluaran);
                        @endphp
                        {{number_format($d->nilai_tagihan + $d->pengeluaran, 0, ',', '.')}}
                    </td>
                {{-- <button class="btn btn-primary">Test</button> --}}
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center align-middle" colspan="3">Grand Total</th>
                    <th class="text-end align-middle">{{number_format($data->sum('nilai_tagihan'), 0, ',', '.')}}</th>
                    <th class="text-end align-middle">{{number_format($modal, 0, ',', '.')}}</th>
                    <th class="text-end align-middle">{{number_format($pph, 0, ',', '.')}}</th>
                    <th class="text-end align-middle">{{number_format($pph_badan, 0, ',', '.')}}</th>
                    <th class="text-end align-middle">{{number_format($profit, 0, ',', '.')}}</th>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
@endsection
@push('css')
<link href="{{asset('assets/css/dt.min.css')}}" rel="stylesheet">
<script src="{{asset('assets/js/cleave.min.js')}}"></script>
@endpush
@push('js')
<script src="{{asset('assets/js/dt5.min.js')}}"></script>
<script>

        $(document).ready(function() {
            var table = $('#data-table').DataTable({
                "paging": false,
                "searching": true,
                "scrollCollapse": true,
                "scrollY": "500px",

            });

            table.on( 'order.dt search.dt', function () {
                table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        });


</script>
@endpush
