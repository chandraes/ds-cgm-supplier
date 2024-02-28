@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1>PENGATURAN</h1>
</div>
<div class="container mt-5">
    <div class="row justify-content-left">
        <div class="col-md-3 text-center mt-3">
            <a href="{{route('pengaturan.akun')}}" class="text-decoration-none">

                <img src="{{asset('images/pengguna.svg')}}" alt="" width="100">
                <h2>AKUN</h2>
            </a>
        </div>
        <div class="col-md-3 text-center mt-3">
            <a href="{{route('pengaturan.wa')}}" class="text-decoration-none">
                <img src="{{asset('images/wa.svg')}}" alt="" width="100">
                <h2>GROUP WHATSAPP</h2>
            </a>
        </div>
        <div class="col-md-3 text-center mt-3">
            <a href="{{route('home')}}" class="text-decoration-none">
                <img src="{{asset('images/dashboard.svg')}}" alt="" width="100">
                <h2>Dashboard</h2>
            </a>
        </div>

    </div>
</div>
@endsection

