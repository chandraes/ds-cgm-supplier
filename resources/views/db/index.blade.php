@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1>DATABASE</h1>
</div>
<div class="container mt-5">
    <div class="row justify-content-left">
        <div class="col-md-3 mt-3 text-center">
            <a href="{{route('db.customer')}}" class="text-decoration-none">
                <img src="{{asset('images/customer.svg')}}" alt="" width="100">
                <h2>CUSTOMER</h2>
            </a>
        </div>
        @if (auth()->user()->role == 'admin')
        <div class="col-md-3 mt-3 text-center">
            <a href="{{route('db.supplier')}}" class="text-decoration-none">
                <img src="{{asset('images/supplier.svg')}}" alt="" width="100">
                <h2>SUPPLIER</h2>
            </a>
        </div>
        <div class="col-md-3 mt-3 text-center">
            <a href="{{route('db.investor')}}" class="text-decoration-none">
                <img src="{{asset('images/investor.svg')}}" alt="" width="100">
                <h2>INVESTOR</h2>
            </a>
        </div>
        <div class="col-md-3 mt-3 text-center">
            <a href="{{route('db.rekening')}}" class="text-decoration-none">
                <img src="{{asset('images/rekening.svg')}}" alt="" width="100">
                <h2>REKENING TRANSAKSI</h2>
            </a>
        </div>
        @endif
        <div class="col-md-3 mt-3 text-center">
            <a href="{{route('home')}}" class="text-decoration-none">
                <img src="{{asset('images/dashboard.svg')}}" alt="" width="100">
                <h2>DASHBOARD</h2>
            </a>
        </div>
    </div>
</div>
@endsection

