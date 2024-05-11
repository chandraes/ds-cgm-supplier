@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mb-5">
        <div class="col-md-12 text-center">
            <h1><u>INVOICE PPH </u></h1>
            <h1>{{$tahun}}</h1>
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
    <div class="row justify-content-between mt-3">
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
        <form action="{{route('rekap.pph-badan')}}" method="get" class="col-md-6">
            <div class="row mt-2">
                <div class="col-md-6 mb-3">
                    <select class="form-select" name="tahun" id="tahun">
                        @foreach ($dataTahun as $d)
                        <option value="{{$d->tahun}}" {{$d->tahun == $tahun ? 'selected' : ''}}>{{$d->tahun}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <button type="submit" class="btn btn-primary form-control" id="btn-cari">Tampilkan</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row mt-3">
        <table class="table table-bordered table-hover" id="data-table">
            <thead class="table-success">
                <tr>
                    <th class="text-center align-middle">No</th>
                    <th class="text-center align-middle">Customer</th>
                    <th class="text-center align-middle">Project</th>
                    <th class="text-center align-middle">Nilai PPH</th>
                </tr>
            </thead>
            <tbody>
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
                        {{$d->nf_nilai_pph}}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center align-middle" colspan="3">Grand Total</th>
                    <th class="text-end align-middle">{{number_format($data->sum('nilai_pph'), 0, ',', '.')}}</th>
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
