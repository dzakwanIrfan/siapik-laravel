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
            <table class="table table-striped table-bordered align-middle table-dark table-hover" id="datatables">
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

    <div class="modal fade" id="submissionModal" tabindex="-1" aria-labelledby="submissionModalLabel" aria-hidden="true" data-url-template="{{ route('submissions.statuses.data', ['submission' => '__ID__']) }}">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
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

    <!-- chat modal -->
    <div class="modal fade" id="chat-modal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chatModalLabel">Diskusi Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="chat-content-container">
                        <p class="text-center">Memuat percakapan...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="chat-form-container" style="width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    const table = $('#datatables').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route('submissions.index.datatable') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'txtReceiptNumber', name: 'txtReceiptNumber' },
            { data: 'letter_type', name: 'letter_type' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'dtmInserted', name: 'dtmInserted', defaultContent: '-' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });

    let statusTable = null;

    $('#datatables tbody').on('click', '.show-status-modal', function(e) {
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

    $('#datatables tbody').on('click', '.chat-btn', function() {
        var submissionId = $(this).data('id');
        var chatUrl = "{{ route('submissions.chat.index', ['submission' => '__ID__']) }}".replace('__ID__', submissionId);

        $('#chat-modal').attr('data-chat-url', chatUrl);
        $('#chat-content-container').html('<p class="text-center">Memuat percakapan...</p>');
        $('#chat-form-container').html('');
        $('#chat-modal').modal('show');

        $.get(chatUrl, function(response) {
            // GANTI .find() MENJADI .filter() DI DUA BARIS INI
            var contentHtml = $(response).filter('.chat-content-wrapper').html();
            var formHtml = $(response).filter('.chat-form-wrapper').html();

            // Suntikkan HTML ke dalam modal
            $('#chat-content-container').html(contentHtml);
            $('#chat-form-container').html(formHtml);

            var chatContent = document.querySelector('#chat-content-container .chat-content');
            chatContent.scrollTop = chatContent.scrollHeight;
        });
    });

    $('#chat-modal').on('click', '#btn-send-chat', function(e) {
        e.preventDefault();

        var form = $(this).closest('form');
        var url = form.attr('action');
        var chatContentUrl = $('#chat-modal').attr('data-chat-url');
        var submitButton = $(this); // Simpan referensi tombol
        var originalButtonText = submitButton.html(); // Simpan teks asli tombol

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),

            // Sebelum permintaan dikirim
            beforeSend: function() {
                // Aktifkan status loading secara manual
                submitButton.prop('disabled', true);
                submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            },

            success: function(data) {
                $('#chat-content-container').load(chatContentUrl + ' .chat-content-wrapper > *', function() {
                    var chatContent = document.querySelector('#chat-content-container .chat-content');
                    chatContent.scrollTop = chatContent.scrollHeight;
                });
                form.trigger("reset");
            },

            error: function() {
                alert('Gagal mengirim pesan.');
            },

            // Setelah permintaan selesai (baik sukses maupun error)
            complete: function() {
                // Nonaktifkan status loading dan kembalikan tombol ke semula
                submitButton.prop('disabled', false);
                submitButton.html(originalButtonText);
            }
        });
    });
});
</script>
@endpush


