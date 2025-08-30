@php($layoutNavbar = true)

@extends('layouts.default_layout')
@section('title', 'Sistem Persuratan')

@section('page_title')
  <!-- BEGIN breadcrumb -->
  <nav aria-label="breadcrumb" class="breadcrumb-header float-end float-lg-end">
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Permintaan Surat Mahasiswa</li>
      </ol>
  </nav>
  <!-- END breadcrumb -->

  <!-- BEGIN page-header -->
  <div>
    <h3>Permintaan Surat Mahasiswa</h3>
    <p class="text-subtitle text-muted">Sistem Informasi Pembuatan Surat</p>
  </div>
  <!-- END page-header -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Permintaan Surat Mahasiswa</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered align-middle table-dark table-hover" id="datatables">
                <thead>
                    <tr class="text-black">
                        <th>No</th>
                        <th>Nomor Pembuatan</th>
                        <th>Pemohon</th>
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

    <div class="modal fade" id="submissionModal" tabindex="-1" aria-labelledby="submissionModalLabel" aria-hidden="true" data-url-template="{{ route('submissions.statuses.data', ['submission' => '__ID__']) }}">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <div>
                        <h5 class="modal-title mb-0">Riwayat Status Pengajuan Surat</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <table id="submission-status-table" class="table table-striped table-bordered align-middle table-dark table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Status</th>
                                <th>Modified By</th>
                                <th>Modified At</th>
                                <th>Being Processed By</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody><!-- server-side --></tbody>
                    </table>
                </div>
                
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Inisialisasi DataTable server-side
    const table = $('#datatables').DataTable({
        serverSide: true,
        processing: true,
        responsive: true,
        colReorder: true,
        keys: true,
        rowReorder: true,
        ajax: '{{ route('kaprodi.submissions.index.datatable') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'txtReceiptNumber', name: 'txtReceiptNumber' },
            { data: 'user_full_name', name: 'user_full_name' },
            { data: 'letter_type', name: 'letter_type' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'dtmInserted', name: 'dtmInserted', defaultContent: 'Not updated yet' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });
</script>

<script>
    $(document).ready(function() {
    let statusTable = null;

    // Event delegation untuk tombol yang dibuat dinamis oleh DataTables
    $(document).on('click', '.show-status-modal', function(e) {
        e.preventDefault();
        
        const submissionId = $(this).data('submissions-id');
        const typeName = $(this).data('type-name') || 'Submission';
        
        if (!submissionId) {
            console.error('Submission ID not found');
            return;
        }
        
        // Set judul modal
        $('#submissionModal .js-type-name').text(typeName);
        
        // Siapkan URL ajax
        const urlTemplate = $('#submissionModal').data('url-template');
        const ajaxUrl = urlTemplate.replace('__ID__', submissionId);
        
        console.log('Ajax URL:', ajaxUrl); // Debug
        
        if (statusTable) {
            // Reload dengan submission yang baru
            statusTable.ajax.url(ajaxUrl).load();
        } else {
            // Init DataTable pertama kali
            statusTable = $('#submission-status-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: ajaxUrl,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'txtStatus', name: 'txtStatus' },
                    { data: 'txtInsertedBy', name: 'txtInsertedBy', defaultContent: '-' },
                    { data: 'dtmInserted', name: 'dtmInserted', defaultContent: '-' },
                    { data: 'txtInReview', name: 'txtInReview', defaultContent: '-' },
                    { data: 'bitActive', name: 'bitActive', orderable: false, searchable: false }
                ],
                order: [[3, 'desc']],
                destroy: true
            });
        }
        
        // Show modal
        $('#submissionModal').modal('show');
    });
});
</script>
@endpush


