@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12 text-center">
            <h1><u>REKAP LABA DISIMPAN</u></h1>
            <h1>{{$stringBulanNow}} {{$tahun}}</h1>
        </div>
    </div>
    @include('swal')
    <div class="flex-row justify-content-between mt-3">
        <div class="col-md-6">
            <table class="table">
                <tr class="text-center">
                    <td><a href="{{route('home')}}"><img src="{{asset('images/dashboard.svg')}}" alt="dashboard" width="30"> Dashboard</a></td>
                    <td><a href="{{route('rekap')}}"><img src="{{asset('images/rekap.svg')}}" alt="dokumen" width="30"> REKAP</a></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="container-fluid mt-5">
    <form action="{{route('rekap.laba-simpan')}}" method="get">
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="bulan" class="form-label">Bulan</label>
                <select class="form-select" name="bulan" id="bulan">
                    <option value="1" {{$bulan == 1 ? 'selected' : ''}}>Januari</option>
                    <option value="2" {{$bulan == 2 ? 'selected' : ''}}>Februari</option>
                    <option value="3" {{$bulan == 3 ? 'selected' : ''}}>Maret</option>
                    <option value="4" {{$bulan == 4 ? 'selected' : ''}}>April</option>
                    <option value="5" {{$bulan == 5 ? 'selected' : ''}}>Mei</option>
                    <option value="6" {{$bulan == 6 ? 'selected' : ''}}>Juni</option>
                    <option value="7" {{$bulan == 7 ? 'selected' : ''}}>Juli</option>
                    <option value="8" {{$bulan == 8 ? 'selected' : ''}}>Agustus</option>
                    <option value="9" {{$bulan == 9 ? 'selected' : ''}}>September</option>
                    <option value="10" {{$bulan == 10 ? 'selected' : ''}}>Oktober</option>
                    <option value="11" {{$bulan == 11 ? 'selected' : ''}}>November</option>
                    <option value="12" {{$bulan == 12 ? 'selected' : ''}}>Desember</option>
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
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary form-control">Tampilkan</button>
            </div>
        </div>
    </form>
</div>
<div class="container-fluid table-responsive ml-3">
    <div class="row mt-3">
        <table class="table table-hover table-bordered" id="rekapTable">
            <thead class="table-success">
                <tr>
                    <th class="text-center align-middle">Tanggal</th>
                    <th class="text-center align-middle">Uraian</th>
                    <th class="text-center align-middle">Masuk</th>
                    <th class="text-center align-middle">Keluar</th>
                    <th class="text-center align-middle">Saldo</th>
                    <th class="text-center align-middle">Nama Rek</th>
                    <th class="text-center align-middle">Bank</th>
                    <th class="text-center align-middle">No. Rek</th>
                </tr>
                <tr class="table-warning">
                    <td colspan="4" class="text-center align-middle">Saldo Bulan {{$stringBulan}} {{$tahunSebelumnya}}</td>
                    <td class="text-end align-middle">Rp. {{$dataSebelumnya ? $dataSebelumnya->nf_saldo : '0'}}</td>
                    <td colspan="3"></td>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                <tr>
                    <td class="text-center align-middle">{{$d->tanggal}}</td>
                    <td class="text-start align-middle">{{$d->uraian}}</td>
                    <td class="text-end align-middle">{{$d->jenis === 'in' ? $d->nf_nominal : ''}}</td>
                    <td class="text-end align-middle text-danger">{{$d->jenis === 'out' ? $d->nf_nominal : ''}}</td>
                    <td class="text-end align-middle">{{$d->nf_saldo}}</td>
                    <td class="text-center align-middle">{{$d->nama_rek}}</td>
                    <td class="text-center align-middle">{{$d->bank}}</td>
                    <td class="text-center align-middle">{{$d->no_rek}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-center align-middle" colspan="2"><strong>GRAND TOTAL</strong></td>
                    <td class="text-end align-middle"><strong>{{number_format($data->where('jenis', 'in')->sum('nominal'), 0, ',', '.')}}</strong></td>
                    <td class="text-end align-middle text-danger"><strong>{{number_format($data->where('jenis', 'out')->sum('nominal'), 0, ',', '.')}}</strong></td>
                    <td class="text-end align-middle"><strong>{{number_format(($dataSebelumnya ? $dataSebelumnya->saldo : 0) + $data->where('jenis', 'in')->sum('nominal') - $data->where('jenis', 'out')->sum('nominal'), 0, ',', '.')}}</strong></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
@push('css')
<link href="{{asset('assets/css/dt.min.css')}}" rel="stylesheet">
@endpush
@push('js')
<script src="{{asset('assets/js/dt5.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#rekapTable').DataTable({
            paging: false,
            ordering: false,
            searching: false,
            scrollCollapse: true,
            scrollY: '550px',
            fixedColumns: {
                leftColumns: 1,
            },
        });
    });
</script>
@endpush
