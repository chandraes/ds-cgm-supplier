@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mb-3">
        <div class="col-md-12 text-center">
            <h1><u>REKAP INVOICE</u></h1>
            <h1>{{strtoupper($stringBulanNow)}} {{$tahun}}</h1>
            <h1>{{$customer->nama}}</h1>
        </div>
        <div class="flex-row justify-content-between mt-3">
            <div class="col-md-6">
                <table class="table">
                    <tr class="text-center">
                        <td><a href="{{route('home')}}"><img src="{{asset('images/dashboard.svg')}}" alt="dashboard"
                                    width="30"> Dashboard</a></td>
                        <td><a href="{{route('rekap')}}"><img src="{{asset('images/rekap.svg')}}" alt="dokumen"
                                    width="30"> REKAP</a></td>
                        {{-- <td>
                            <a href="{{route('rekap.kas-besar.print', ['bulan' => $bulan, 'tahun' => $tahun])}}" target="_blank"><img src="{{asset('images/print.svg')}}" alt="dokumen"
                                width="30"> PRINT PDF</a>
                        </td> --}}
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <form action="{{route('rekap.invoice', ['customer' => $customer->id])}}" method="get">
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
    <div class="row mt-3">
        <table class="table table-bordered table-hover" id="tableTransaksi">
            <thead class="table-success">
                <tr>
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
                    <th class="text-center align-middle">Total Bayar</th>
                    <th class="text-center align-middle">Total PPN</th>
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
                    <td class="text-center align-middle">{{$d->nf_pph}}</td>
                    <td class="text-center align-middle">{{$d->profit}}</td>
                    <td class="text-center align-middle">{{number_format($d->total_tagihan,0,',','.')}}</td>
                    <td class="text-center align-middle">{{number_format($d->total_bayar,0,',','.')}}</td>
                    <td class="text-center align-middle">{{number_format($d->total_ppn,0,',','.')}}</td>

                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-center align-middle">Grand Total</th>
                    <th class="text-center align-middle">{{number_format($totalBerat, 0, ',','.')}}</th>
                    <th class="text-center align-middle">Kg</th>
                    <th class="text-center align-middle"></th>
                    <th class="text-center align-middle">{{number_format($total, 0,',','.')}}</th>
                    <th class="text-center align-middle">{{number_format($totalPPH, 0,',','.')}}</th>
                    <th class="text-center align-middle">{{$totalProfit}}</th>
                    <th class="text-center align-middle">{{number_format($totalTagihan, 0, ',','.')}}</th>
                    <th class="text-center align-middle">{{number_format($totalBayar, 0, ',','.')}}</th>
                    <th class="text-center align-middle">{{number_format($totalPPN, 0, ',','.')}}</th>
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
            var table = $('#tableTransaksi').DataTable({
                "paging": false,
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
