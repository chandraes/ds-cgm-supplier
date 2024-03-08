@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center mb-5">
        <div class="col-md-12 text-center">
            <h1><u>NOTA TAGIHAN</u></h1>
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
                    <td><a href="{{route('billing')}}"><img src="{{asset('images/billing.svg')}}" alt="dokumen"
                                width="30"> Billing</a></td>

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
                    <th class="text-center align-middle">Total Tagihan</th>
                    <th class="text-center align-middle">Balance</th>
                    <th class="text-center align-middle">Sisa Tagihan</th>
                    <th class="text-center align-middle">Lunas</th>
                    <th class="text-center align-middle">Cicil</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                <tr>
                    <td class="text-center align-middle"></td>
                    <td class="text-center align-middle">{{$d->customer->nama}}</td>
                    <td class="text-start align-middle">{{$d->project->nama}}</td>

                    <td class="text-end align-middle">
                        {{number_format($d->nilai_tagihan, 0, ',', '.')}}
                    </td>
                    <td class="text-end align-middle">
                        {{number_format($d->dibayar, 0, ',', '.')}}
                    </td>
                    <td class="text-end align-middle">
                        {{number_format($d->sisa_tagihan, 0, ',', '.')}}
                    </td>
                    <td class="text-center align-middle">
                        <form action="" method="post" id="lunasForm-{{$d->id}}">
                        @csrf
                            <button type="submit" class="btn btn-success">Pelunasan </button>
                        </form>
                    </td>
                    <td class="text-center align-middle">
                        <!-- Modal trigger button -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cicil-{{$d->id}}">
                          Cicilan
                        </button>

                        <!-- Modal Body -->
                        <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
                        <div class="modal fade" id="cicil-{{$d->id}}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalTitleId">Jumlah Cicilan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="" method="post" id="cicilForm-{{$d->id}}">
                                        @csrf
                                    <div class="modal-body">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">Rp</span>
                                            <input type="text" class="form-control @if ($errors->has('nominal_transaksi'))
                                            is-invalid
                                        @endif" name="cicilan" id="cicilanInput-{{$d->id}}" required data-thousands="." >
                                          </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>

                    </td>
                </tr>
                {{-- <button class="btn btn-primary">Test</button> --}}
                <script>
                     $('#lunasForm-{{$d->id}}').submit(function(e){
                        e.preventDefault();
                        Swal.fire({
                            title: 'Apakah anda yakin?',
                            text: "Pelunasan Tagihan sebesar Rp. {{number_format($d->sisa_tagihan, 0, ',', '.')}}",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, simpan!'
                            }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        })
                    });

                    $('#cicilForm-{{$d->id}}').submit(function(e){
                        e.preventDefault();
                        Swal.fire({
                            title: 'Apakah anda yakin?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, simpan!'
                            }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        })
                    });
                </script>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center align-middle" colspan="3"> Total</th>
                    <th class="text-end align-middle">{{number_format($data->sum('nilai_tagihan'), 0, ',', '.')}}</th>
                    <th class="text-end align-middle">{{number_format($data->sum('dibayar'), 0, ',', '.')}}</th>
                    <th class="text-end align-middle">{{number_format($data->sum('sisa_tagihan'), 0, ',', '.')}}</th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
@endsection
@push('css')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{asset('assets/css/dt.min.css')}}" rel="stylesheet">
@endpush
@push('js')
<script src="{{asset('assets/js/dt5.min.js')}}"></script>
<script src="{{asset('assets/js/cleave.min.js')}}"></script>
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

        $('#editForm').submit(function(e){
            e.preventDefault();
            Swal.fire({
                title: 'Apakah data sudah benar?',
                text: "Pastikan data sudah benar sebelum disimpan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, simpan!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $('#spinner').show();
                    this.submit();
                }
            })
        });

        $('#masukForm').submit(function(e){
            e.preventDefault();
            Swal.fire({
                title: 'Apakah data sudah benar?',
                text: "Pastikan data sudah benar sebelum disimpan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, simpan!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $('#spinner').show();
                    this.submit();
                }
            })
        });

        $('#lanjutkanForm').submit(function(e){
            var value = $('#total_tagih_display').val();
            var check = $('#total_tagih').val();

            if (check == 0 || check == '') {
                Swal.fire({
                    title: 'Tidak ada data yang dipilih!',
                    text: "Harap pilih tagihan terlebih dahulu!",
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ok'
                    })
                return false;

            }

            e.preventDefault();
            Swal.fire({
                title: 'Apakah data sudah benar?',
                text: "Total Tagihan Rp. "+value,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, simpan!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $('#spinner').show();
                    this.submit();
                }
            })
        });
</script>
@endpush
