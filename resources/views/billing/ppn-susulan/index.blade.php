@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center mb-5">
        <div class="col-md-12 text-center">
            <h1><u>PPn Masukan Susulan</u></h1>
        </div>
    </div>
    @if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{session('error')}}',
        })
    </script>
    @endif
    <form action="{{route('ppn-susulan.store')}}" method="post" id="masukForm">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="uraian" class="form-label">Tanggal</label>
                <input type="text" class="form-control @if ($errors->has('uraian'))
                    is-invalid
                @endif" name="uraian" id="uraian" required value="{{date('d M Y')}}" disabled>
            </div>
            <div class="col-md-6 mb-3">
                <label for="nominal" class="form-label">Nominal</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Rp</span>
                    <input type="text" class="form-control @if ($errors->has('nominal'))
                    is-invalid
                @endif" name="nominal" id="nominal" required data-thousands="." value="{{old('nominal')}}">
                </div>
                @if ($errors->has('nominal'))
                <div class="invalid-feedback">
                    {{$errors->first('nominal')}}
                </div>
                @endif
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6 mb-3 text-center">
                <label for="nama" class="form-label">PENGELOLA ({{$pp}}%)</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Rp</span>
                    <input type="text" class="form-control" name="nilai-pengelola" required data-thousands="." value="{{old('nominal')}}" disabled
                        id="nilai-pengelola">
                </div>
            </div>
            <div class="col-md-6 mb-3 text-center">
                <label for="nama" class="form-label">INVESTOR ({{$pi}}%)</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Rp</span>
                    <input type="text" class="form-control" name="nilai-investor" required data-thousands="." value="{{old('nominal')}}" disabled
                        id="nilai-investor">
                </div>
            </div>
            <hr>
            <h2 class="text-center">INVESTOR</h2>
            @foreach ($im as $i)
            <div class="col-md-6 mb-3 text-center">
                <label for="nominal" class="form-label">{{$i->nama}} ({{$i->persentase}}%)</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Rp</span>
                    <input type="text" class="form-control @if ($errors->has('nominal'))
                            is-invalid
                        @endif" name="nilai" required data-thousands="." value="{{old('nominal')}}" disabled
                        id="nilai-{{$i->id}}">
                </div>
            </div>
            @endforeach
        </div>
        <div class="d-grid gap-3 mt-3">
            <button class="btn btn-success" type="submit" id="btnSubmit">Simpan</button>
            <a href="{{route('pajak.index')}}" class="btn btn-secondary" type="button">Batal</a>
        </div>
    </form>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{asset('assets/plugins/select2/select2.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/plugins/select2/select2.min.css')}}">
@endpush
@push('js')
<script src="{{asset('assets/plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/cleave.min.js')}}"></script>
<script>
    $('#investor_modal_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih Investor Modal'
        });

        var nominal = new Cleave('#nominal', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            delimiter: '.'
        });

        $('#nominal').on('keyup', function(){
            let valAwal = $(this).val().replace(/\./g,'');
            let val = valAwal * ({{$pi}}/100);

            let nilaiPengelola = valAwal * ({{$pp}}/100);
            let nilaiInvestor = valAwal * ({{$pi}}/100);

            $('#nilai-pengelola').val(nilaiPengelola.toLocaleString('id-ID'));

            $('#nilai-investor').val(nilaiInvestor.toLocaleString('id-ID'));

            let dataInvestor = {!! json_encode($im) !!};

            dataInvestor.forEach(function(investor) {
                let persen = investor.persentase;
                let hasil = val * persen / 100;

                $('#nilai-'+investor.id).val(hasil.toLocaleString('id-ID'));

            });
        });
        // masukForm on submit, sweetalert confirm
        $('#masukForm').submit(function(e){
            e.preventDefault();
            Swal.fire({
                title: 'Apakah data sudah benar?',
                text: "Pastikan data sudah benar sebelum disimpan!",
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
