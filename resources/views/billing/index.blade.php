@extends('layouts.app')
@section('content')
<div class="container text-center">
    <h1>BILLING</h1>
</div>
@include('swal')
<div class="container mt-5">
    <div class="row justify-content-left">
        <div class="col-lg-3 col-md-3 col-sm-6 text-center mt-5">
            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#formDeposit">
                <img src="{{asset('images/form-deposit.svg')}}" alt="" width="100">
                <h2>FORM DEPOSIT</h2>
            </a>
            @include('billing.modal-form-deposit')
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 text-center mt-5">
            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#formKecil">
                <img src="{{asset('images/form-kas-kecil.svg')}}" alt="" width="100">
                <h2>FORM KAS KECIL</h2>
            </a>
            @include('billing.modal-form-kas-kecil')
        </div>
        @if (auth()->user()->role == 'admin' || auth()->user()->role == 'su')
        <div class="col-lg-3 col-md-3 col-sm-6 text-center mt-5">
            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalTransaksi">
                <img src="{{asset('images/transaksi.svg')}}" alt="" width="100">
                <h2>FORM TRANSAKSI</h2>
            </a>
            @include('billing.modal-form-transaksi')

        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 text-center mt-5">
            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalLain">
                <img src="{{asset('images/form-lain.svg')}}" alt="" width="100">
                <h2>FORM LAIN-LAIN</h2>
            </a>
            <div class="modal fade" id="modalLain" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
                role="dialog" aria-labelledby="modalLainTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLainTitle">Form Lain-lain</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <select class="form-select" name="selectLain" id="selectLain">
                                <option value="masuk">Dana Masuk</option>
                                <option value="keluar">Dana Keluar</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-primary" onclick="funLain()">Lanjutkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
    <div class="row justify-content-left">

        <div class="col-lg-3 col-md-3 col-sm-6 text-center mt-5">
            <a href="{{route('nota-tagihan.index')}}" class="text-decoration-none">
                <img src="{{asset('images/nota-tagihan.svg')}}" alt="" width="100">
                <h2>NOTA TAGIHAN @if($nt != 0) <span class="text-danger">({{$nt}})</span> @endif</h2>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 text-center mt-5">
            <a href="{{route('nota-ppn-masukan')}}" class="text-decoration-none">
                <img src="{{asset('images/form-ppn.svg')}}" alt="" width="100">
                <h2>NOTA PPn MASUKAN @if($np != 0) <span class="text-danger">({{$np}})</span> @endif</h2>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 text-center mt-5">
            <a href="{{route('invoice-tagihan')}}" class="text-decoration-none">
                <img src="{{asset('images/invoice-tagihan.svg')}}" alt="" width="100">
                <h2>INVOICE TAGIHAN @if($it != 0) <span class="text-danger">({{$it}})</span> @endif</h2>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 text-center mt-5">
            <a href="{{route('invoice-ppn')}}" class="text-decoration-none">
                <img src="{{asset('images/taxes.svg')}}" alt="" width="100">
                <h2>INVOICE PPN @if($ip != 0) <span class="text-danger">({{$ip}})</span> @endif</h2>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 text-center mt-5">
            <a href="{{route('ppn-susulan')}}" class="text-decoration-none">
                <img src="{{asset('images/ppn-susulan.svg')}}" alt="" width="100">
                <h2>PPN MASUKAN SUSULAN</h2>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 text-center mt-5">
            <a href="{{route('home')}}" class="text-decoration-none">
                <img src="{{asset('images/dashboard.svg')}}" alt="" width="100">
                <h2>DASHBOARD</h2>
            </a>
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
    function funDeposit(){
        var selectDeposit = document.getElementById('selectDeposit').value;
        if(selectDeposit == 'masuk'){
            window.location.href = "{{route('form-deposit.masuk')}}";
        }else if(selectDeposit == 'keluar'){
            window.location.href = "{{route('form-deposit.keluar')}}";
        }else if(selectDeposit == 'keluar-all'){
            window.location.href = "{{route('form-deposit.keluar-all')}}";
        }
    }

    function funLain(){
        var selectLain = document.getElementById('selectLain').value;
        if(selectLain == 'masuk'){
            window.location.href = "{{route('form-lain.masuk')}}";
        }else if(selectLain == 'keluar'){
            window.location.href = "{{route('form-lain.keluar')}}";
        }
    }

    function funKecil(){
        var selectKecil = document.getElementById('selectKecil').value;
        if(selectKecil == 'masuk'){
            window.location.href = "{{route('form-kas-kecil.masuk')}}";
        }else if(selectKecil == 'keluar'){
            window.location.href = "{{route('form-kas-kecil.keluar')}}";
        }
    }

    function funTransaksi(){
        var selectTransaksi = document.getElementById('selectTransaksi').value;
        if(selectTransaksi == 'masuk'){
            window.location.href = "{{route('form-transaksi.masuk')}}";
        }else if(selectTransaksi == 'keluar'){
            window.location.href = "{{route('form-transaksi.index')}}";
        }
    }
</script>
@endpush
