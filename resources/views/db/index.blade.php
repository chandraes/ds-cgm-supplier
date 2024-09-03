@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1><u>DATABASE</u></h1>
</div>
<div class="container mt-5">
    <div class="row justify-content-left">
        <h2>Data Lama</h2>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="{{route('db.investor')}}" class="text-decoration-none">
                <img src="{{asset('images/investor.svg')}}" alt="" width="70">
                <h4 class="mt-2">PERSENTASE DIVIDEN</h4>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="{{route('db.investor-modal')}}" class="text-decoration-none">
                <img src="{{asset('images/investor-modal.svg')}}" alt="" width="70">
                <h4 class="mt-2">INVESTOR MODAL</h4>
            </a>
        </div>
    </div>
    <hr>
    <div class="row justify-content-left">
        <h2>Data Internal</h2>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="#" class="text-decoration-none">
                <img src="{{asset('images/kosong.svg')}}" alt="" width="70">
                <h4 class="mt-3">PERSENTASE DIVIDEN<br>PENGELOLA & INVESTOR</h4>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="#" class="text-decoration-none">
                <img src="{{asset('images/kosong.svg')}}" alt="" width="70">
                <h4 class="mt-3">PERSENTASE DIVIDEN<br>PENGELOLA</h4>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="#" class="text-decoration-none">
                <img src="{{asset('images/kosong.svg')}}" alt="" width="70">
                <h4 class="mt-3">PERSENTASE DIVIDEN<br>INVESTOR</h4>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="#" class="text-decoration-none">
                <img src="{{asset('images/kosong.svg')}}" alt="" width="70">
                <h4 class="mt-3">BIODATA & GAJI<br>DIREKSI</h4>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="#" class="text-decoration-none">
                <img src="{{asset('images/kosong.svg')}}" alt="" width="70">
                <h4 class="mt-3">BIODATA & GAJI<br>STAFF</h4>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="#" class="text-decoration-none">
                <img src="{{asset('images/kosong.svg')}}" alt="" width="70">
                <h4 class="mt-3">BONUS STAFF</h4>
            </a>
        </div>
    </div>
    <hr>
    <div class="row justify-content-left">
        <h2>Data Eksternal</h2>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="#" class="text-decoration-none">
                <img src="{{asset('images/kosong.svg')}}" alt="" width="70">
                <h4 class="mt-3">BIODATA SUPPLIER</h4>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="{{route('db.customer')}}" class="text-decoration-none">
                <img src="{{asset('images/customer.svg')}}" alt="" width="70">
                <h4 class="mt-2">BIODATA CUSTOMER</h4>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="{{route('db.rekening')}}" class="text-decoration-none">
                <img src="{{asset('images/rekening.svg')}}" alt="" width="70">
                <h4 class="mt-2">REKENING TRANSAKSI</h4>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="#" class="text-decoration-none">
                <img src="{{asset('images/kosong.svg')}}" alt="" width="70">
                <h4 class="mt-3">PERSENTASE PAJAK</h4>
            </a>
        </div>
    </div>
    <hr>
    <div class="row justify-content-left mt-4">
        <h2>Data Kategori</h2>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="#" class="text-decoration-none">
                <img src="{{asset('images/kosong.svg')}}" alt="" width="70">
                <h4 class="mt-2">KATEGORI COST OPERATIONAL</h4>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="#" class="text-decoration-none">
                <img src="{{asset('images/kosong.svg')}}" alt="" width="70">
                <h4 class="mt-2">KATEGORI INVENTARIS</h4>
            </a>
        </div>
    </div>
    <hr>
    <div class="row justify-content-left">
        <h2>Data Transaksi</h2>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="{{route('db.project')}}" class="text-decoration-none">
                <img src="{{asset('images/project.svg')}}" alt="" width="70">
                <h4 class="mt-2">PROJECT</h4>
            </a>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 my-4 text-center">
            <a href="{{route('home')}}" class="text-decoration-none">
                <img src="{{asset('images/dashboard.svg')}}" alt="" width="70">
                <h4 class="mt-2">DASHBOARD</h4>
            </a>
        </div>
    </div>
</div>
@endsection

