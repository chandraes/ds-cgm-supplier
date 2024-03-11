@extends('layouts.app')
@section('content')
<div class="container text-center">
    <h1>DASHBOARD</h1>
</div>
<div class="container mt-5">
    <div class="row justify-content-left">
        @if (auth()->user()->role != 'supplier' && auth()->user()->role != 'investor')
        <div class="col-md-3 text-center mb-5 mt-3">
            <a href="{{route('db')}}" class="text-decoration-none">
                <img src="{{asset('images/database.svg')}}" alt="" width="100">
                <h2>DATABASE</h2>
            </a>
        </div>
        @endif
        @if (auth()->user()->role != 'supplier' && auth()->user()->role != 'investor')
        <div class="col-md-3 text-center mb-5 mt-3">
            <a href="{{route('billing')}}" class="text-decoration-none">
                <img src="{{asset('images/billing.svg')}}" alt="" width="100">
                <h2>BILLING</h2>
            </a>
        </div>
        @endif
        @if (auth()->user()->role != 'supplier')
        <div class="col-md-3 text-center mb-5 mt-3">
            <a href="{{route('rekap')}}" class="text-decoration-none">
                <img src="{{asset('images/rekap.svg')}}" alt="" width="100">
                <h2>REKAP</h2>
            </a>
        </div>
        @endif
        @if (auth()->user()->role == 'admin' || auth()->user()->role == 'su')
        <div class="col-md-3 text-center mb-5 mt-3">
            <a href="{{route('pengaturan')}}" class="text-decoration-none">
                <img src="{{asset('images/pengaturan.svg')}}" alt="" width="100">
                <h2>PENGATURAN</h2>
            </a>
        </div>
        @endif
        @if (auth()->user()->role == 'supplier')
        <div class="col-md-3 text-center mb-5 mt-3">
            <a href="{{route('kas-per-supplier')}}" class="text-decoration-none">
                <img src="{{asset('images/kas-supplier.svg')}}" alt="" width="100">
                <h2>KAS SUPPLIER</h2>
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

