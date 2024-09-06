@extends('layouts.app')
@section('content')
<div class="container text-center">
    <h1><u>PAJAK</u></h1>
</div>
<div class="container mt-3">
    <div class="row justify-content-left">
         <div class="col-md-3 text-center mt-3">
            <a href="{{route('nota-ppn-masukan')}}" class="text-decoration-none">
                <img src="{{asset('images/form-ppn.svg')}}" alt="" width="70">
                <h4 class="mt-3">NOTA PPn MASUKAN @if($np != 0) <span class="text-danger">({{$np}})</span> @endif</h4>
            </a>
        </div>

         <div class="col-md-3 text-center mt-3">
            <a href="{{route('invoice-ppn')}}" class="text-decoration-none">
                <img src="{{asset('images/taxes.svg')}}" alt="" width="70">
                <h4 class="mt-3">INVOICE PPN @if($ip != 0) <span class="text-danger">({{$ip}})</span> @endif</h4>
            </a>
        </div>
         <div class="col-md-3 text-center mt-3">
            <a href="{{route('ppn-susulan')}}" class="text-decoration-none">
                <img src="{{asset('images/ppn-susulan.svg')}}" alt="" width="70">
                <h4 class="mt-3">PPN MASUKAN SUSULAN</h4>
            </a>
        </div>
         <div class="col-md-3 text-center mt-3">
            <a href="{{route('pph-disimpan')}}" class="text-decoration-none">
                <img src="{{asset('images/pajak.svg')}}" alt="" width="70">
                <h4 class="mt-3">PPh DISIMPAN @if($pph != 0) <span class="text-danger">({{$pph}})</span> @endif</h4>
            </a>
        </div>
        <div class="col-md-3 text-center mt-3">
            <a href="{{route('home')}}" class="text-decoration-none">
                <img src="{{asset('images/dashboard.svg')}}" alt="" width="70">
                <h4 class="mt-2">DASHBOARD</h4>
            </a>
        </div>
    </div>
    </div>
</div>
@endsection
