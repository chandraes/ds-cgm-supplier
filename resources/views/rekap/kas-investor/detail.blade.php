@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center mb-5">
        <div class="col-md-12 text-center">
            <h1><u>HISTORY INVESTOR</u></h1>
        </div>
    </div>
    {{-- if has any error --}}
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
                    <td><a href="{{route('rekap')}}"><img src="{{asset('images/rekap.svg')}}" alt="dokumen" width="30">
                            REKAP</a></td>
                    <td><a href="{{route('rekap.kas-investor')}}"><img src="{{asset('images/kas-investor.svg')}}"
                                alt="dokumen" width="30"> REKAP Investor</a></td>


                </tr>
            </table>
        </div>
    </div>
    <div class="row mt-3">
        <table class="table table-bordered table-hover" id="data-table">
            <thead class="table-success">
                <tr>
                    <th class="text-center align-middle">Tanggal</th>
                    <th class="text-center align-middle">Uraian</th>
                    <th class="text-center align-middle">Nominal</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($data->kasBesar as $d)
                <tr>
                    <td class="text-center align-middle">{{$d->tanggal}}</td>
                    <td class="text-start align-middle">
                        @switch($d->jenis)
                        @case(1)
                        {{$d->kode_deposit}}
                        @break
                        @default
                        {{$d->uraian}}
                        @endswitch
                    </td>
                    <td class="text-center align-middle">
                        @switch($d->jenis)
                        @case(1)
                        {{$d->nf_nominal}}
                        @break
                        @default
                        -{{$d->nf_nominal}}
                        @endswitch
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center align-middle" colspan="2">Total</th>
                    <th class="text-center align-middle">{{number_format($total, 0, ',','.')}}</th>
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
            var table = $('#data-table').DataTable();

            // table.on( 'order.dt search.dt', function () {
            //     table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            //         cell.innerHTML = i+1;
            //     } );
            // } ).draw();
        });

</script>
@endpush
