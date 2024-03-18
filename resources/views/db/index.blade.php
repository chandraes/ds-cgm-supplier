@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1>DATABASE</h1>
</div>
<div class="container mt-5">
    <div class="row justify-content-left">

        @if (auth()->user()->role == 'admin' || auth()->user()->role == 'su')
        <div class="col-lg-4 my-4 text-center">
            <a href="{{route('db.investor-modal')}}" class="text-decoration-none">
                <img src="{{asset('images/investor-modal.svg')}}" alt="" width="100">
                <h2>INVESTOR MODAL</h2>
            </a>
        </div>
        <div class="col-lg-4 my-4 text-center">
            <a href="{{route('db.investor')}}" class="text-decoration-none">
                <img src="{{asset('images/investor.svg')}}" alt="" width="100">
                <h2>PERSENTASE DEVIDEN</h2>
            </a>
        </div>
        <div class="col-lg-4 my-4 text-center">
            <a href="{{route('db.rekening')}}" class="text-decoration-none">
                <img src="{{asset('images/rekening.svg')}}" alt="" width="100">
                <h2>REKENING TRANSAKSI</h2>
            </a>
        </div>
        @endif
    </div>
    <div class="row justify-content-left">
        <div class="col-lg-4 my-4 text-center">
            <a href="{{route('db.customer')}}" class="text-decoration-none">
                <img src="{{asset('images/customer.svg')}}" alt="" width="100">
                <h2>CUSTOMER</h2>
            </a>
        </div>
        <div class="col-lg-4 my-4 text-center">
            <a href="{{route('db.project')}}" class="text-decoration-none">
                <img src="{{asset('images/project.svg')}}" alt="" width="100">
                <h2>PROJECT</h2>
            </a>
        </div>
        <div class="col-lg-4 my-4 text-center">
            <a href="{{route('home')}}" class="text-decoration-none">
                <img src="{{asset('images/dashboard.svg')}}" alt="" width="100">
                <h2>DASHBOARD</h2>
            </a>
        </div>
    </div>
</div>
@endsection

