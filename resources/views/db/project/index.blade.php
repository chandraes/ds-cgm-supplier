@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12 text-center">
            <h1><u>PROJECT</u></h1>
        </div>
    </div>
    <div class="flex-row justify-content-between mt-3">
        <div class="col-md-6">
            <table class="table" id="data-table">
                <tr>
                    <td><a href="{{route('home')}}"><img src="{{asset('images/dashboard.svg')}}" alt="dashboard"
                                width="30"> Dashboard</a></td>
                    <td><a href="{{route('db')}}"><img src="{{asset('images/database.svg')}}" alt="dokumen" width="30">
                            Database</a></td>
                    <td><a href="#" data-bs-toggle="modal" data-bs-target="#createCustomer"><img
                                src="{{asset('images/project.svg')}}" width="30"> Tambah Project</a>

                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@include('db.project.create')
@include('db.project.edit')
<div class="container-fluid mt-5 table-responsive">
    <table class="table table-bordered table-hover" id="data">
        <thead class="table-warning bg-gradient">
            <tr>
                <th class="text-center align-middle" style="width: 5%">NO</th>
                <th class="text-center align-middle">KODE</th>
                <th class="text-center align-middle">CUSTOMER</th>
                <th class="text-center align-middle">NAMA PROJECT</th>
                <th class="text-center align-middle">NO KONTRAK</th>
                <th class="text-center align-middle">NILAI DPP</th>
                <th class="text-center align-middle">TGL MULAI</th>
                <th class="text-center align-middle">TGL JATUH TEMPO</th>
                <th class="text-center align-middle">PPn</th>
                <th class="text-center align-middle">PPh</th>
                <th class="text-center align-middle">STATUS</th>
                <th class="text-center align-middle">ACT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
            <tr>
                <td class="text-center align-middle">{{$loop->iteration}}</td>
                <td class="text-center align-middle">{{$d->kode}}</td>
                <td class="text-center align-middle">{{$d->customer->singkatan}}</td>
                <td class="text-center align-middle">{{$d->nama}}</td>
                <td class="text-center align-middle">{{$d->nomor_kontrak}}</td>
                <td class="text-end align-middle">{{$d->nf_nilai}}</td>
                <td class="text-center align-middle">{{$d->id_tanggal_mulai}}</td>
                <td class="text-center align-middle">{{$d->id_jatuh_tempo}}</td>
                <td class="text-center align-middle">
                    {{-- checked icon if ppn == 1 --}}
                    @if ($d->ppn == 1)
                    <i class="fa fa-check"></i>
                    @endif
                </td>
                <td class="text-center align-middle">
                    {{-- checked icon if ppn == 1 --}}
                    @if ($d->pph == 1)
                    <i class="fa fa-check"></i>
                    @endif
                </td>
                <td class="text-center align-middle">
                    <button
                        class="btn {{ $d->project_status_id == 1 ? 'btn-warning' : ($d->project_status_id == 3 ? 'btn-success' : 'btn-danger') }}">
                        {{$d->project_status->nama_status}}
                    </button>
                </td>
                <td class="text-center align-middle">
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-primary m-2" data-bs-toggle="modal"
                            data-bs-target="#editProject" onclick="editProject({{$d}}, {{$d->id}})"><i
                                class="fa fa-edit"></i></button>
                        <form action="{{route('db.project.delete', $d)}}" method="post" id="deleteForm-{{$d->id}}">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger m-2"><i class="fa fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <script>
                 $('#deleteForm-{{$d->id}}').submit(function(e){
                    e.preventDefault();
                    Swal.fire({
                        title: 'Apakah anda yakin?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!'
                        }).then((result) => {
                        if (result.isConfirmed) {
                            $('#spinner').show();
                            this.submit();
                        }
                    })
            });
            </script>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
@push('css')
<link href="{{asset('assets/css/dt.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('assets/js/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/plugins/select2/select2.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/plugins/select2/select2.min.css')}}">
@endpush
@push('js')
<script src="{{asset('assets/js/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/js/cleave.min.js')}}"></script>
<script src="{{asset('assets/plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/dt5.min.js')}}"></script>
<script>
    $('#createForm').submit(function(e){
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

    function editProject(data, id) {
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_customer_id').value = data.customer_id;
        document.getElementById('edit_nilai').value = data.nf_nilai;
        document.getElementById('edit_nomor_kontrak').value = data.nomor_kontrak;
        document.getElementById('edit_tanggal_mulai').value = data.id_tanggal_mulai;
        document.getElementById('edit_jatuh_tempo').value = data.id_jatuh_tempo;
        if (data.ppn == 1) {
            document.getElementById('edit_ppn').checked = true;
        }
        if (data.pph == 1) {
            document.getElementById('edit_pph').checked = true;
        }
        document.getElementById('editForm').action = '/db/project/' + id + '/update';
    };

    $('#customer_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $('#createCustomer')
    });

    $('#data').DataTable({
        paging: false,
        scrollCollapse: true,
        scrollY: "550px",
    });


    var harga = new Cleave('#nilai', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: ',',
        delimiter: '.'
    });

    var editNilai = new Cleave('#edit_nilai', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: ',',
        delimiter: '.'
    });

    flatpickr("#tanggal_mulai", {
            dateFormat: "d-m-Y",
        });

    flatpickr("#jatuh_tempo", {
        dateFormat: "d-m-Y",
    });

    flatpickr("#edit_tanggal_mulai", {
            dateFormat: "d-m-Y",
        });

    flatpickr("#edit_jatuh_tempo", {
        dateFormat: "d-m-Y",
    });

    $('#editForm').submit(function(e){
            e.preventDefault();
            var form = this; // Store a reference to the form

            // Close the Bootstrap modal
            $('#editProject').modal('hide');
            Swal.fire({
                title: 'Enter Password',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                preConfirm: (password) => {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: '{{route('pengaturan.password-konfirmasi-cek')}}',
                            type: 'POST',
                            data: JSON.stringify({ password: password }),
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('#editForm').data('csrf-token')
                            },
                            success: function(data) {
                                if (data.status === 'success') {
                                    resolve();
                                } else {
                                    // swal show error message\
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: data.message
                                    });
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: textStatus
                                    });
                            }
                        });
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#spinner').show();
                    this.submit();
                }
                $('#editProject').modal('show');
            });
        });


</script>
@endpush
