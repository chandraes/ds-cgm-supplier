@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mb-5">
        <div class="col-md-12 text-center">
            <h1><u>NOTA TAGIHAN</u></h1>
            <h1>{{$customer->nama}}</h1>
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
    {{-- end if --}}
    <div class="row">
        <div class="col-md-6">
            <label for="berat" class="form-label">Total Tagihan di Pilih</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">Rp.</span>
                <input type="text" class="form-control" id="total_tagih_display" disabled >
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <table class="table table-bordered table-hover" id="tableTransaksi">
            <thead class="table-success">
                <tr>
                    <th class="text-center align-middle" style="width:3%">
                        Select
                        <input type="checkbox" onclick="checkAll(this)" id="checkAll">
                    </th>
                    <th class="text-center align-middle">No</th>
                    <th class="text-center align-middle">Tanggal</th>
                    <th class="text-center align-middle">Supplier</th>
                    <th class="text-center align-middle">Nota Timbangan</th>
                    <th class="text-center align-middle">Berat</th>
                    <th class="text-center align-middle" style="width: 3%">Sat</th>
                    <th class="text-center align-middle">Harga Satuan</th>
                    <th class="text-center align-middle">Total Harga</th>
                    <th class="text-center align-middle">PPH 0,25%</th>
                    <th class="text-center align-middle">Profit</th>
                    <th class="text-center align-middle">Total Tagihan</th>
                    <th class="text-center align-middle">Act</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                <tr>
                    <td class="text-center align-middle">
                        {{-- checklist on check push $d->id to $selectedData --}}
                        <input type="checkbox" value="{{$d->id}}" data-tagihan="{{$d->total_tagihan}}" onclick="check(this, {{$d->id}})" id="idSelect-{{$d->id}}">
                    </td>
                    <td class="text-center align-middle"></td>
                    <td class="text-center align-middle">{{$d->id_tanggal}}</td>
                    <td class="text-center align-middle">{{$d->supplier->nickname}}</td>
                    <td class="text-center align-middle">{{$d->nota_timbangan}}</td>
                    <td class="text-center align-middle">{{$d->nf_berat}}</td>
                    <td class="text-center align-middle">Kg</td>
                    <td class="text-center align-middle">{{$d->nf_harga}}</td>
                    <td class="text-center align-middle">{{$d->nf_total}}</td>
                    <td class="text-center align-middle">{{$d->nf_pph}}</td>
                    <td class="text-center align-middle">{{$d->nf_profit}}</td>
                    <td class="text-center align-middle">{{number_format($d->total_tagihan,0,',','.')}}</td>
                    <td class="text-center align-middle">
                        @if (auth()->user()->role == 'admin')
                        <button class="btn m-2 btn-warning" data-bs-toggle="modal" data-bs-target="#editTransaksi" onclick="editTransaksi({{$d}}, {{$d->id}})"><i class="fa fa-edit"></i></button>
                        <form action="{{route('form-transaksi.delete', ['transaksi' => $d->id])}}" method="post" style="display: inline-block;"
                            id="delete-{{$d->id}}">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                        </form>
                        @endif
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
                    <th colspan="5" class="text-center align-middle">Grand Total</th>
                    <th class="text-center align-middle">{{$totalBerat}}</th>
                    <th class="text-center align-middle">Kg</th>
                    <th class="text-center align-middle"></th>
                    <th class="text-center align-middle">{{$total}}</th>
                    <th class="text-center align-middle">{{$totalPPH}}</th>
                    <th class="text-center align-middle">{{$totalProfit}}</th>
                    <th class="text-center align-middle">{{number_format($totalTagihan, 0, ',','.')}}</th>
                    <th class="text-center align-middle"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    @include('billing.nota-tagihan.edit')
    <div class="row mt-5">
        <form action="{{route('nota-tagihan.cutoff', ['customer' => $customer->id])}}" method="post" id="lanjutkanForm">
        @csrf
            <input type="hidden" name="customer_id" value="{{$customer->id}}">
            <input type="hidden" name="selectedData" required>
            <input type="hidden" class="form-control" id="total_tagih" name="total_tagih" required value="0">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary form-control">LANJUTKAN</button>
            </div>
        </form>
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

            document.getElementById('editForm').action = '/billing/nota-tagihan/edit/' + id;
        }

       function check(checkbox, id) {
            var totalTagihan = parseFloat($('#total_tagih').val()) || 0;
            var tagihan = parseFloat($(checkbox).data('tagihan'));

            if (checkbox.checked) {
                totalTagihan += tagihan;

                $('input[name="selectedData"]').val(function(i, v) {
                    return v + id + ',';
                });
            } else {
                totalTagihan -= tagihan;

                $('input[name="selectedData"]').val(function(i, v) {
                    return v.replace(id + ',', '');
                });
            }

            $('#total_tagih').val(totalTagihan);
            $('#total_tagih_display').val(totalTagihan.toLocaleString('id-ID'));

            var value = $('input[name="selectedData"]').val();

            if(value.slice(-1) == ','){
                value = value.slice(0, -1);
            }

            console.log(value);
        }

        $( function() {

            $( "#edit_tanggal" ).datepicker({
                dateFormat: "dd-mm-yy"
            });
        });

            // check all checkbox and push all id to $selectedData and check all checkbox
        function checkAll(checkbox) {
            // empty total tagih dan total tagih display
            $('#total_tagih').val(0);
            $('#total_tagih_display').val(0);
            $('input[name="selectedData"]').val('');
            var totalTagihan = parseFloat($('#total_tagih').val()) || 0;

            if (checkbox.checked) {
                $('input[name="selectedData"]').val(function(i, v) {
                    @foreach ($data as $d)
                        var tagihan = parseFloat($('#idSelect-{{$d->id}}').data('tagihan'));
                        totalTagihan += tagihan;

                        v = v + {{$d->id}} + ',';
                        $('#idSelect-{{$d->id}}').prop('checked', true);
                    @endforeach
                    return v;
                });
            } else {
                $('input[name="selectedData"]').val(function(i, v) {
                    @foreach ($data as $d)

                        v = v.replace({{$d->id}} + ',', '');
                        $('#idSelect-{{$d->id}}').prop('checked', false);
                    @endforeach
                    return v;
                });
                totalTagihan = 0;
            }

            $('#total_tagih').val(totalTagihan);
            $('#total_tagih_display').val(totalTagihan.toLocaleString('id-ID'));

            var value = $('input[name="selectedData"]').val();

            if(value.slice(-1) == ','){
                value = value.slice(0, -1);
            }

            console.log(value);
        }

        var edit_nota = new Cleave('#edit_nota_timbangan', {
            delimiter: '-',
            blocks: [4, 4]
        });

        var editBerat = new Cleave('#edit_berat', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            delimiter: '.'
        });

        $(document).ready(function() {
            var table = $('#tableTransaksi').DataTable({
                "paging": false,
                "searching": false,
                "scrollCollapse": true,
                "scrollY": "500px",

            });

            table.on( 'order.dt search.dt', function () {
                table.column(1, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
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
