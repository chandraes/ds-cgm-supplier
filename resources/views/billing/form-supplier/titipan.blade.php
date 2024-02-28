@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center mb-5">
        <div class="col-md-12 text-center">
            <h1><u>Titipan Supplier</u></h1>
        </div>
    </div>
    @include('swal')
    <form action="{{route('form-supplier.titipan-store')}}" method="post" id="masukForm">
        @csrf
        <div class="row">
            <div class="col-md-2 mb-3">
                <label for="nomor" class="form-label">Kode</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">T</span>
                    <input type="text" class="form-control" name="nomor" id="nomor" disabled value="{{str_pad($nomor, 2, '0', STR_PAD_LEFT)}}" >
                  </div>
            </div>
            <div class="col-md-2 mb-3">
                <label for="uraian" class="form-label">Tanggal</label>
                <input type="text" class="form-control @if ($errors->has('uraian'))
                    is-invalid
                @endif" name="uraian" id="uraian" required value="{{date('d M Y')}}" disabled>
            </div>
            <div class="col-md-4 mb-3">
                <div class="mb-3">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    <select class="form-select" name="supplier_id" id="supplier_id" onchange="funRek()" required>
                        <option value="">-- Pilih Supplier -- </option>
                        @foreach ($data as $d)
                            <option value="{{$d->id}}">{{$d->nama}} ({{$d->nickname}})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="nominal_transaksi" class="form-label">Nominal</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Rp</span>
                    <input type="text" class="form-control @if ($errors->has('nominal_transaksi'))
                    is-invalid
                @endif" name="nominal_transaksi" id="nominal_transaksi" required data-thousands="." >
                  </div>
                @if ($errors->has('nominal_transaksi'))
                <div class="invalid-feedback">
                    {{$errors->first('nominal_transaksi')}}
                </div>
                @endif
            </div>
        </div>
        <hr>
        <h3>Transfer Ke</h3>
        <br>
        <div class="row">

            <div class="col-md-4 mb-3">
                <label for="transfer_ke" class="form-label">Nama</label>
                <input type="text" class="form-control @if ($errors->has('transfer_ke'))
                    is-invalid
                @endif" name="transfer_ke" id="transfer_ke" disabled>
                @if ($errors->has('transfer_ke'))
                <div class="invalid-feedback">
                    {{$errors->first('transfer_ke')}}
                </div>
                @endif
            </div>
            <div class="col-md-4 mb-3">
                <label for="bank" class="form-label">Bank</label>
                <input type="text" class="form-control @if ($errors->has('bank'))
                    is-invalid
                @endif" name="bank" id="bank" disabled >
                @if ($errors->has('bank'))
                <div class="invalid-feedback">
                    {{$errors->first('bank')}}
                </div>
                @endif
            </div>
            <div class="col-md-4 mb-3">
                <label for="no_rekening" class="form-label">Nomor Rekening</label>
                <input type="text" class="form-control @if ($errors->has('no_rekening'))
                    is-invalid
                @endif" name="no_rekening" id="no_rekening" disabled>
            </div>
        </div>

        <div class="d-grid gap-3 mt-3">
            <button class="btn btn-success" type="submit">Simpan</button>
            <a href="{{route('billing')}}" class="btn btn-secondary" type="button">Batal</a>
          </div>
    </form>
</div>
@endsection
@push('js')
    <script src="{{asset('assets/js/cleave.min.js')}}"></script>
    {{-- select2 --}}
    <script src="{{asset('assets/plugins/select2/select2.full.min.js')}}"></script>
    <script>
         var nominal = new Cleave('#nominal_transaksi', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            delimiter: '.'
        });

        function funRek(){
            var supplier_id = document.getElementById('supplier_id').value;
            if(supplier_id == ''){
                $('#transfer_ke').val('');
                $('#bank').val('');
                $('#no_rekening').val('');
                return;
            } else {
                $.ajax({
                    url: "{{route('form-supplier.get-rek-supplier', '')}}"+"/"+supplier_id,
                    type: "GET",
                    dataType: "JSON",
                    success: function(data){
                        $('#transfer_ke').val(data.nama_rek);
                        $('#bank').val(data.bank);
                        $('#no_rekening').val(data.no_rek);
                    },
                });
            }

        }

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
