@extends('layouts.app')
@section('content')

<div class="container text-center">
    <h1>REKAP</h1>
</div>
<div class="container mt-5">
    <div class="row justify-content-left">
        <div class="col-lg-3 mt-3 mb-3 text-center">
            <a href="{{route('rekap.kas-besar')}}" class="text-decoration-none">
                <img src="{{asset('images/kas-besar.svg')}}" alt="" width="80">
                <h3>KAS BESAR</h3>
            </a>
        </div>
        <div class="col-lg-3 mt-3 mb-3 text-center">
            <a href="{{route('rekap.kas-kecil')}}" class="text-decoration-none">
                <img src="{{asset('images/kas-kecil.svg')}}" alt="" width="80">
                <h3>KAS KECIL</h3>
            </a>
        </div>
        <div class="col-lg-3 mt-3 mb-3 text-center">
            <a href="{{route('rekap.kas-investor')}}" class="text-decoration-none">
                <img src="{{asset('images/kas-investor.svg')}}" alt="" width="80">
                <h3>KAS INVESTOR</h3>
            </a>
        </div>
        <div class="col-lg-3 mt-3 mb-3 text-center">
            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#kasSupplier">
                <img src="{{asset('images/kas-supplier.svg')}}" alt="" width="80">
                <h3>KAS PROJECT</h3>
            </a>
            <div class="modal fade" id="kasSupplier" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
                role="dialog" aria-labelledby="kasSupplierTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="kasSupplierTitle">Kas Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{route('rekap.kas-project')}}" method="get">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <select class="form-select" name="project" id="project" required>
                                        <option value="">-- Pilih Project --</option>
                                        @foreach ($project as $i)
                                        <option value="{{$i->id}}">{{$i->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Lanjutkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-left mt-3">
        <div class="col-lg-3 mt-3 mb-3 text-center">
            <a href="{{route('rekap.invoice')}}" class="text-decoration-none">
                <img src="{{asset('images/rekap-invoice.svg')}}" alt="" width="80">
                <h3>INVOICE</h3>
            </a>
        </div>
        <div class="col-lg-3 mt-3 mb-3 text-center">
            <a href="{{route('rekap.invoice-pph')}}" class="text-decoration-none">
                <img src="{{asset('images/pajak.svg')}}" alt="" width="80">
                <h3>INVOICE PPH</h3>
            </a>
        </div>
        <div class="col-lg-3 mt-3 mb-3 text-center">
            <a href="{{route('rekap.pph-badan')}}" class="text-decoration-none">
                <img src="{{asset('images/pajak-tahunan.svg')}}" alt="" width="80">
                <h3>PPH BADAN</h3>
            </a>
        </div>
        <div class="col-lg-3 mt-3 mb-3 text-center">
            <a href="{{route('home')}}" class="text-decoration-none">
                <img src="{{asset('images/dashboard.svg')}}" alt="" width="80">
                <h3>DASHBOARD</h3>
            </a>
        </div>
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{asset('assets/plugins/select2/select2.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/plugins/select2/select2.min.css')}}">
@endpush
@push('js')
<script src="{{asset('assets/plugins/select2/select2.full.min.js')}}"></script>
<script>
      $('#project').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#kasSupplier')
        });
</script>
@endpush
