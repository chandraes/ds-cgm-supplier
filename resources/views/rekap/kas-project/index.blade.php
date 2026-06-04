@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12 text-center">
            <h1><u>REKAP KAS PROJECT</u></h1>
            <h4>Semua Transaksi</h4>
        </div>
    </div>
    @include('swal')
    <div class="flex-row justify-content-between mt-3">
        <div class="col-md-6">
            <table class="table">
                <tr class="text-center">
                    <td><a href="{{route('home')}}"><img src="{{asset('images/dashboard.svg')}}" alt="dashboard"
                                width="30"> Dashboard</a></td>
                    <td><a href="{{route('rekap')}}"><img src="{{asset('images/rekap.svg')}}" alt="dokumen"
                                width="30"> REKAP</a></td>
                    <td>
                        <a href="{{route('rekap.kas-project.print', ['project' => $project->id,'bulan' => $bulan, 'tahun' => $tahun])}}" target="_blank"><img src="{{asset('images/print.svg')}}" alt="dokumen"
                            width="30"> PRINT PDF</a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="container-fluid table-responsive ml-3">
    <div class="row mx-5">
        <div class="col-md-6 d-flex justify-content-start">
            <table>
                <tr>
                    <th>Customer</th>
                    <th>:</th>
                    <th>{{$project->customer->nama}}</th>
                </tr>
                <tr>
                    <th>Nama Project</th>
                    <th style="width: 1rem">:</th>
                    <th>{{$project->nama}}</th>
                </tr>
                <tr>
                    <th>Nomor Kontrak</th>
                    <th>:</th>
                    <th>{{$project->nomor_kontrak}}</th>
                </tr>

            </table>
        </div>
           <div class="col-md-6 d-flex justify-content-end">
            <table>
                <tr>
                    <th>Nilai Kontrak</th>
                    <th>:</th>
                    <th>Rp {{$project->nf_total_tagihan}}</th>
                </tr>
                <tr>
                    <th>Tanggal PO</th>
                    <th>:</th>
                    <th>{{$project->id_tanggal_mulai}}</th>
                </tr>
                <tr>
                    <th>Tanggal Jatuh Tempo</th>
                    <th style="width: 20px">:</th>
                    <th>{{$project->id_jatuh_tempo}}</th>
                </tr>

            </table>
        </div>
    </div>
    <div style="display: flex; justify-content: flex-end;">

    </div>

    <div class="row mt-3">

        <table class="table table-hover table-bordered" id="rekapTable">
            <thead class=" table-success">
            <tr>
                <th class="text-center align-middle">Tanggal</th>
                <th class="text-center align-middle">Uraian</th>
                <th class="text-center align-middle">Masuk</th>
                <th class="text-center align-middle">Keluar</th>
                <th class="text-center align-middle">Sisa</th>
                <th class="text-center align-middle">Transfer Ke Rekening</th>
                <th class="text-center align-middle">Bank</th>
                @if ($project->project_status_id != 2)
                    <th class="text-center align-middle">ACT</th>
                @endif
            </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <td class="text-center align-middle" colspan="2"><strong>GRAND TOTAL</strong></td>
                    <td class="text-end align-middle"><strong id="grand-masuk"></strong></td>
                    <td class="text-end align-middle text-danger"><strong id="grand-keluar"></strong></td>
                    <td class="text-end align-middle"><strong id="last-sisa"></strong></td>
                    <td></td>
                    <td></td>
                    @if ($project->project_status_id != 2)
                        <td></td>
                    @endif
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
@push('css')
<link href="{{asset('assets/css/dt.min.css')}}" rel="stylesheet">
@endpush
@push('js')
<script src="{{asset('assets/plugins/date-picker/date-picker.js')}}"></script>
<script src="{{asset('assets/js/dt5.min.js')}}"></script>
<script>
    $(document).ready(function() {
        const ajaxUrl = '{{ route('rekap.kas-project', ['project' => $project->id]) }}';
        const csrfToken = '{{ csrf_token() }}';
        const voidRouteTemplate = '{{ route('rekap.kas-project.void', ['kasProject' => '__ID__']) }}';
        const pageSize = 50;
        const hasVoidColumn = {{ $project->project_status_id != 2 ? 'true' : 'false' }};

        let currentPage = 0;
        let lastPage = 1;
        let isLoading = false;

        const columns = [
            { data: 'tanggal', className: 'text-center align-middle' },
            { data: 'uraian', className: 'text-start align-middle' },
            {
                data: null,
                className: 'text-end align-middle',
                render: function(data) {
                    return data.jenis === 1 ? data.nf_nominal : '';
                }
            },
            {
                data: null,
                className: 'text-end align-middle',
                render: function(data) {
                    return data.jenis === 0 ? '<span class="text-danger">' + data.nf_nominal + '</span>' : '';
                }
            },
            { data: 'nf_sisa', className: 'text-end align-middle' },
            { data: 'nama_rek', className: 'text-center align-middle' },
            { data: 'bank', className: 'text-center align-middle' }
        ];

        if (hasVoidColumn) {
            columns.push({
                data: 'id',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: function(data, type, row) {
                    if (row.void === 0) {
                        const url = voidRouteTemplate.replace('__ID__', data);
                        return '<form action="' + url + '" method="POST" class="void-form" data-id="' + data + '">' +
                            '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                            '<button type="submit" class="btn btn-danger btn-sm">Void</button>' +
                            '</form>';
                    }
                    return '';
                }
            });
        }

        const table = $('#rekapTable').DataTable({
            paging: false,
            ordering: false,
            searching: false,
            scrollCollapse: true,
            scrollY: '550px',
            deferRender: true,
            autoWidth: false,
            columns: columns
        });

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value || 0);
        }

        function updateFooter(totals) {
            $('#grand-masuk').text(formatNumber(totals.grand_total_masuk));
            $('#grand-keluar').text(formatNumber(totals.grand_total_keluar));
            $('#last-sisa').text(formatNumber(totals.last_sisa));
        }

        function loadPage(page) {
            if (isLoading || page > lastPage) {
                return;
            }

            isLoading = true;

            $.getJSON(ajaxUrl, {
                page: page,
                length: pageSize,
            }, function(response) {
                currentPage = response.current_page;
                lastPage = response.last_page;

                if (currentPage === 1) {
                    updateFooter(response);
                }

                table.rows.add(response.data).draw(false);
            })
            .always(function() {
                isLoading = false;
            });
        }

        $('#rekapTable tbody').on('submit', '.void-form', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Uraian: Void',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, simpan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $('#rekapTable').closest('.dataTables_wrapper').find('.dataTables_scrollBody').on('scroll', function() {
            const scrollBody = this;
            if (scrollBody.scrollTop + scrollBody.clientHeight >= scrollBody.scrollHeight - 10) {
                loadPage(currentPage + 1);
            }
        });

        loadPage(1);
    });
</script>
@endpush
