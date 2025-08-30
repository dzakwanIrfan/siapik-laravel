@php($layoutNavbar = true)

@extends('layouts.default_layout')
@section('title', 'Sistem Persuratan')

@section('page_title')
  <!-- BEGIN breadcrumb -->
  <nav aria-label="breadcrumb" class="breadcrumb-header float-end float-lg-end">
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Riwayat Pengajuan</li>
      </ol>
  </nav>
  <!-- END breadcrumb -->

  <!-- BEGIN page-header -->
  <div>
    <h3>Riwayat Pengajuan</h3>
    <p class="text-subtitle text-muted">Sistem Informasi Pembuatan Surat</p>
  </div>
  <!-- END page-header -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Riwayat Pengajuan</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle text-nowrap table-dark table-hover" id="datatables">
                    <thead>
                        <tr class="text-black">
                            <th>No</th>
                            <th>Nomor Pembuatan</th>
                            <th>Nama Surat</th>
                            <th>Status</th>
                            <th>Tanggal Pembuatan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody> 
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Inisialisasi DataTable server-side
    const table = $('#datatables').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        colReorder: true,
        keys: true,
        rowReorder: true,
        ajax: '{{ route('submissions.index.datatable') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'txtReceiptNumber', name: 'txtReceiptNumber' },
            { data: 'letter_type', name: 'letter_type' },
            { data: 'txtStatus', name: 'txtStatus' },
            { data: 'dtmInserted', name: 'dtmInserted', defaultContent: 'Not updated yet' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        dom: '<"row mb-3"<"col-lg-8 d-lg-block"<"d-flex d-lg-inline-flex justify-content-center mb-md-2 mb-lg-0 me-0 me-md-3"l><"d-flex d-lg-inline-flex justify-content-center mb-md-2 mb-lg-0 "B>><"col-lg-4 d-flex d-lg-block justify-content-center"fr>>t<"row mt-3"<"col-md-auto me-auto"i><"col-md-auto ms-auto"p>>',
        buttons: [
            { extend: 'copy', className: 'btn-sm' },
            { extend: 'csv', className: 'btn-sm' },
            { extend: 'excel', className: 'btn-sm' },
            { extend: 'pdf', className: 'btn-sm' },
            { extend: 'print', className: 'btn-sm' }
        ],
        order: [[1, 'asc']]
    });
</script>
@endpush


