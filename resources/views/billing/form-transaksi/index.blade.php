@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mb-5">
        <div class="col-md-12 text-center">
            <h1><u>Tambah Transaksi</u></h1>
            <h1>{{$customer->nama}}</h1>
        </div>
    </div>
    <form action="{{route('form-transaksi.tambah-store')}}" method="post" id="masukForm">
        @csrf
        <div class="row">
            <div class="col-md-2 mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="text" class="form-control @if ($errors->has('tanggal'))
                    is-invalid
                @endif" name="tanggal" id="tanggal" required @if (session('tgl')) value="{{session('tgl')}}" @endif >
            </div>
            <div class="col-md-3 mb-3">
                <div class="mb-3">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    <select class="form-select" name="supplier_id" id="supplier_id" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($project as $s)
                        <option value="{{$s->id}}" {{session('supplier') == $s->id ? 'selected' : ''}}>{{$s->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="nota_timbangan" class="form-label">Nota Timbang</label>
                <input type="text" class="form-control @if ($errors->has('nota_timbangan'))
                    is-invalid
                @endif" name="nota_timbangan" id="nota_timbangan" required>
                @if ($errors->has('nota_timbangan'))
                <div class="invalid-feedback">
                    {{$errors->first('nota_timbangan')}}
                </div>
                @endif
            </div>
            <div class="col-md-3 mb-3">
                <label for="berat" class="form-label">Berat Bersih (Netto)</label>
                <div class="input-group mb-3">

                    <input type="text" class="form-control @if ($errors->has('berat'))
                    is-invalid
                @endif" name="berat" id="berat" required data-thousands="." >
                    <span class="input-group-text" id="basic-addon1">Kg</span>
                  </div>
                @if ($errors->has('berat'))
                <div class="invalid-feedback">
                    {{$errors->first('berat')}}
                </div>
                @endif
            </div>

        </div>
        <div class="row justify-content-center">
            <div class="col-md-3 text-center mb-3">
                {{-- simpan --}}
                <label for="berat" class="form-label">&nbsp;</label>
                <input type="hidden" name="customer_id" value="{{$customer->id}}">
                <button type="submit" class="btn btn-primary form-control">Simpan</button>
            </div>
        </div>
    </form>
    <hr>
    <br>
    <div class="row mt-3">
        <table class="table table-bordered table-hover" id="tableTransaksi">
            <thead class="table-success">
                <tr>
                    <th class="text-center align-middle">No</th>
                    <th class="text-center align-middle">Tanggal</th>
                    <th class="text-center align-middle">Supplier</th>
                    <th class="text-center align-middle">Nota Timbangan</th>
                    <th class="text-center align-middle">Berat</th>
                    <th class="text-center align-middle">Sat</th>
                    <th class="text-center align-middle">Harga Satuan</th>
                    <th class="text-center align-middle">Total Harga</th>
                    <th class="text-center align-middle">Act</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                <tr>
                    <td class="text-center align-middle"></td>
                    <td class="text-center align-middle">{{$d->id_tanggal}}</td>
                    <td class="text-center align-middle">{{$d->supplier->nickname}}</td>
                    <td class="text-center align-middle">{{$d->nota_timbangan}}</td>
                    <td class="text-center align-middle">{{$d->nf_berat}}</td>
                    <td class="text-center align-middle">Kg</td>
                    <td class="text-center align-middle">{{$d->nf_harga}}</td>
                    <td class="text-center align-middle">{{$d->nf_total}}</td>
                    <td class="text-center align-middle">
                        <button class="btn m-2 btn-warning" data-bs-toggle="modal" data-bs-target="#editTransaksi" onclick="editTransaksi({{$d}}, {{$d->id}})"><i class="fa fa-edit"></i></button>
                        <form action="{{route('form-transaksi.delete', ['transaksi' => $d->id])}}" method="post" id="delete-{{$d->id}}" style="display: inline-block;">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <script>
                    $(document).ready(function(){
                        $('#delete-{{$d->id}}').submit(function(e){
                            e.preventDefault();
                            Swal.fire({
                                title: 'Apakah anda yakin?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Ya, hapus!'
                                }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#spinner').show();
                                    this.submit();
                                }
                            })
                        });
                    });
                </script>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-center align-middle">Grand Total</th>
                    <th class="text-center align-middle">{{$beratTotal}}</th>
                    <th class="text-center align-middle">Kg</th>
                    <th class="text-center align-middle"></th>
                    <th class="text-center align-middle">{{$total}}</th>
                    <th class="text-center align-middle"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    @include('billing.form-transaksi.edit')
    <div class="row mt-5">
        {{-- button lanjutkan --}}
            <form action="{{route('form-transaksi.lanjutkan', ['customer' => $customer->id])}}" method="post" id="lanjutkanForm">
                @csrf
                <button type="submit" class="btn btn-success form-control" id="lanjutkan">Lanjutkan</button>
            </form>
            {{-- button back --}}
            <div class="col-md-12">
                <a href="{{route('billing')}}" class="btn btn-secondary form-control mt-3">Kembali</a>
            </div>

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

        function editTransaksi(data, id) {
            let date = new Date(data.tanggal);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            let year = date.getFullYear();

            document.getElementById('edit_tanggal').value = `${day}-${month}-${year}`;
            document.getElementById('edit_supplier_id').value = data.supplier_id;
            document.getElementById('edit_nota_timbangan').value = data.nota_timbangan;
            document.getElementById('edit_berat').value = data.berat.toLocaleString('id');

            document.getElementById('editForm').action = '/billing/form-transaksi/edit/' + id;
        }

        $(document).ready(function() {
                var table = $('#tableTransaksi').DataTable({
                    "paging": false,
                    "searching": false,
                    "scrollCollapse": true,
                    "scrollY": "550px",
                });

                // add no column to table
                table.on( 'order.dt search.dt', function () {
                    table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                        cell.innerHTML = i+1;
                    } );
                } ).draw();

            });

        $( function() {
            $( "#tanggal" ).datepicker({
                dateFormat: "dd-mm-yy"
            });

            $( "#edit_tanggal" ).datepicker({
                dateFormat: "dd-mm-yy"
            });
        });

        var editWa = new Cleave('#nota_timbangan', {
            delimiter: '-',
            blocks: [4, 4]
        });

        var edit_nota = new Cleave('#edit_nota_timbangan', {
            delimiter: '-',
            blocks: [4, 4]
        });

        var nominal = new Cleave('#berat', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            delimiter: '.'
        });

        var editBerat = new Cleave('#edit_berat', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            delimiter: '.'
        });
        // masukForm on submit, sweetalert confirm
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

        $('#lanjutkanForm').submit(function(e){
            e.preventDefault();
            Swal.fire({
                title: 'Pastikan harga sudah benar!!! Harga customer sebesar Rp. {{$customer->harga}}!',
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
