@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mb-5">
        <div class="col-md-12 text-center">
            <h1><u>INVOICE PPN</u></h1>
            <h1>{{strtoupper($stringBulan)}} {{$tahun}}</h1>
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
    <form action="{{route('invoice-ppn.index', ['customer' => $customer->id])}}" method="get">
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="bulan" class="form-label">Bulan</label>
                <select class="form-select" name="bulan" id="bulan">
                    <option value="1" {{$bulan=='01' ? 'selected' : '' }}>Januari</option>
                    <option value="2" {{$bulan=='02' ? 'selected' : '' }}>Februari</option>
                    <option value="3" {{$bulan=='03' ? 'selected' : '' }}>Maret</option>
                    <option value="4" {{$bulan=='04' ? 'selected' : '' }}>April</option>
                    <option value="5" {{$bulan=='05' ? 'selected' : '' }}>Mei</option>
                    <option value="6" {{$bulan=='06' ? 'selected' : '' }}>Juni</option>
                    <option value="7" {{$bulan=='07' ? 'selected' : '' }}>Juli</option>
                    <option value="8" {{$bulan=='08' ? 'selected' : '' }}>Agustus</option>
                    <option value="9" {{$bulan=='09' ? 'selected' : '' }}>September</option>
                    <option value="10" {{$bulan=='10' ? 'selected' : '' }}>Oktober</option>
                    <option value="11" {{$bulan=='11' ? 'selected' : '' }}>November</option>
                    <option value="12" {{$bulan=='12' ? 'selected' : '' }}>Desember</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="tahun" class="form-label">Tahun</label>
                <select class="form-select" name="tahun" id="tahun">
                    @foreach ($dataTahun as $d)
                    <option value="{{$d->tahun}}" {{$d->tahun == $tahun ? 'selected' : ''}}>{{$d->tahun}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="tahun" class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary form-control" id="btn-cari">Tampilkan</button>
            </div>
        </div>
    </form>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <label for="berat" class="form-label"><strong> Total Tagihan di Pilih </strong></label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">Rp.</span>
                <input type="text" class="form-control" id="total_tagih_display" disabled>
            </div>
        </div>
        <div class="col-md-4">
            <label for="total_pph" class="form-label"><strong> Total PPh di Pilih </strong></label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">Rp.</span>
                <input type="text" class="form-control" id="total_pph_display" disabled>
            </div>
        </div>
        <div class="col-md-4">
            <label for="total_pph" class="form-label"><strong> Total PPN di Pilih </strong></label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">Rp.</span>
                <input type="text" class="form-control" id="total_ppn_display" disabled>
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
                    <th class="text-center align-middle">Total PPN</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                <tr>
                    <td class="text-center align-middle">
                        {{-- checklist on check push $d->id to $selectedData --}}
                        <input type="checkbox" value="{{$d->id}}" data-tagihan="{{$d->total_tagihan}}"
                            data-pph="{{$d->pph}}" data-ppn="{{$d->total_ppn}}" onclick="check(this, {{$d->id}})"
                            id="idSelect-{{$d->id}}">
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
                    <td class="text-center align-middle">{{number_format($d->profit,0,',','.')}}</td>
                    <td class="text-center align-middle">{{number_format($d->total_tagihan,0,',','.')}}</td>
                    <td class="text-center align-middle">{{number_format($d->total_ppn,0,',','.')}}</td>

                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-center align-middle">Grand Total</th>
                    <th class="text-center align-middle">{{number_format($totalBerat, 0, ',','.')}}</th>
                    <th class="text-center align-middle">Kg</th>
                    <th class="text-center align-middle"></th>
                    <th class="text-center align-middle">{{number_format($total, 0,',','.')}}</th>
                    <th class="text-center align-middle">{{number_format($totalPPH, 0,',','.')}}</th>
                    <th class="text-center align-middle">{{number_format($totalProfit, 0, ',','.')}}</th>
                    <th class="text-center align-middle">{{number_format($totalTagihan, 0, ',','.')}}</th>
                    <th class="text-center align-middle">{{number_format($totalPPN, 0, ',','.')}}</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <input type="hidden" id="total_pph" name="total_pph" value="0">
    <div class="row mt-5">
        <form action="{{route('invoice-ppn.cutoff', ['customer' => $customer->id])}}" method="post" id="lanjutkanForm">
            @csrf
            <input type="hidden" name="bulan" value="{{$stringBulan}}">
            <input type="hidden" name="tahun" value="{{$tahun}}">
            <input type="hidden" name="customer_id" value="{{$customer->id}}">
            <input type="hidden" name="selectedData" required>
            <input type="hidden" class="form-control" id="total_ppn" name="total_ppn" required value="0">
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
    function check(checkbox, id) {
            var totalTagihan = parseFloat($('#total_tagih').val()) || 0;
            var totalPph = parseFloat($('#total_pph').val()) || 0;
            var totalPpn = parseFloat($('#total_ppn').val()) || 0;


            var tagihan = parseFloat($(checkbox).data('tagihan'));
            var pph = parseFloat($(checkbox).data('pph'));
            var ppn = parseFloat($(checkbox).data('ppn'));

            if (checkbox.checked) {
                totalTagihan += tagihan;
                totalPph += pph;
                totalPpn += ppn;

                $('input[name="selectedData"]').val(function(i, v) {
                    return v + id + ',';
                });
            } else {
                totalTagihan -= tagihan;
                totalPph -= pph;
                totalPpn -= ppn;

                $('input[name="selectedData"]').val(function(i, v) {
                    return v.replace(id + ',', '');
                });
            }

            $('#total_tagih').val(totalTagihan);
            $('#total_tagih_display').val(totalTagihan.toLocaleString('id-ID'));
            $('#total_pph').val(totalPph);
            $('#total_pph_display').val(totalPph.toLocaleString('id-ID'));
            $('#total_ppn').val(totalPpn);
            $('#total_ppn_display').val(totalPpn.toLocaleString('id-ID'));

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
            $('#total_ppn').val(0);
            $('#total_ppn_display').val(0);
            $('#total_pph').val(0);
            $('#total_pph_display').val(0);

            $('input[name="selectedData"]').val('');
            var totalTagihan = parseFloat($('#total_tagih').val()) || 0;
            var totalPph = parseFloat($('#total_pph').val()) || 0;
            var totalPpn = parseFloat($('#total_ppn').val()) || 0;

            if (checkbox.checked) {
                $('input[name="selectedData"]').val(function(i, v) {
                    @foreach ($data as $d)
                        var tagihan = parseFloat($('#idSelect-{{$d->id}}').data('tagihan'));
                        var pph = parseFloat($('#idSelect-{{$d->id}}').data('pph'));
                        var ppn = parseFloat($('#idSelect-{{$d->id}}').data('ppn'));
                        totalTagihan += tagihan;
                        totalPph += pph;
                        totalPpn += ppn;

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
                totalPph = 0;
                totalPpn = 0;
            }

            $('#total_tagih').val(totalTagihan);
            $('#total_tagih_display').val(totalTagihan.toLocaleString('id-ID'));
            $('#total_pph').val(totalPph);
            $('#total_pph_display').val(totalPph.toLocaleString('id-ID'));
            $('#total_ppn').val(totalPpn);
            $('#total_ppn_display').val(totalPpn.toLocaleString('id-ID'));

            var value = $('input[name="selectedData"]').val();

            if(value.slice(-1) == ','){
                value = value.slice(0, -1);
            }

            console.log(value);
        }

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

        $('#lanjutkanForm').submit(function(e){
            var value = $('#total_ppn_display').val();
            var check = $('#total_ppn').val();

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
