@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mb-5">
        <div class="col-md-12 text-center">
            <h1><u>PPH DISIMPAN</u></h1>
        </div>
    </div>
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
                    <td><a href="{{route('billing')}}"><img src="{{asset('images/billing.svg')}}" alt="dokumen"
                                width="30"> BILLING</a></td>

                </tr>
            </table>
        </div>
    </div>
    <div class="row mt-3">
        <table class="table table-bordered table-hover" id="data-table">
            <thead class="table-success">
                <tr>
                    <th class="text-center align-middle">Select</th>
                    <th class="text-center align-middle">No</th>
                    <th class="text-center align-middle">Customer</th>
                    <th class="text-center align-middle">Project</th>
                    <th class="text-center align-middle">Nilai PPH</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                <tr>
                    <td class="text-center align-middle" style="width: 15px">
                        <input type="checkbox" name="checks" id="check-pph" style="font-size: 20px">
                    </td>
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
                    <th class="text-center align-middle" colspan="4">Grand Total</th>
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
                table.column(1, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        });


</script>
@endpush
