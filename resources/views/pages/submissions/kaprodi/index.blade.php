@php($layoutNavbar = true)

@extends('layouts.default_layout')
@section('title', 'Sistem Persuratan')

@section('page_title')
  <!-- BEGIN breadcrumb -->
  <nav aria-label="breadcrumb" class="breadcrumb-header float-end float-lg-end">
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
      </ol>
  </nav>
  <!-- END breadcrumb -->

  <!-- BEGIN page-header -->
  <div>
    <h3>{{ $pageTitle }}</h3>
    <p class="text-subtitle text-muted">{{ $pageDescription }}</p>
  </div>
  <!-- END page-header -->
@endsection

@section('content')
    @if($status === 'proses')
        <div class="alert alert-primary color-primary">
            <i class="bi bi-exclamation-triangle"></i>&nbsp;&nbsp;{{ $alertMessage }}
        </div>
    @else
        <div class="alert alert-success color-success">
            <i class="bi bi-check-circle"></i>&nbsp;&nbsp;{{ $alertMessage }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data {{ $pageTitle }}</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered align-middle table-dark table-hover" id="datatables">
                <thead>
                    <tr class="text-black">
                        <th>No</th>
                        <th>Nomor Pembuatan</th>
                        <th>Pemohon</th>
                        <th>Nama {{ ucfirst($type) }}</th>
                        <th>Status</th>
                        <th>Tanggal Pembuatan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Modal Status Submission -->
    <div class="modal fade" id="submissionModal" tabindex="-1" aria-labelledby="submissionModalLabel" aria-hidden="true" data-url-template="{{ route('submissions.statuses.data', ['submission' => '__ID__']) }}">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <div>
                        <h5 class="modal-title mb-0">Riwayat Status Pengajuan {{ ucfirst($type) }}</h5>
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
                                <th>Next Process</th>
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

    <!-- Modal Attachment -->
    <div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <div>
                        <h5 class="modal-title mb-0" id="attachmentModalLabel">Lampiran Dokumen</h5>
                        <small class="text-muted" id="attachmentSubmissionInfo"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4" id="attachmentContent">
                    <div class="text-center py-5" id="attachmentLoading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat lampiran...</p>
                    </div>
                    <div id="attachmentList" style="display: none;"></div>
                    <div id="attachmentError" style="display: none;">
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="errorMessage">Gagal memuat lampiran</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk preview file -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filePreviewModalLabel">Preview File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="filePreviewContent" class="text-center p-4 bg-light" style="min-height: 600px;"></div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="downloadFileLink" class="btn btn-primary" target="_blank">
                        <i class="fas fa-download me-2"></i>Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal chat --}}
    <div class="modal fade" id="chat-modal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chatModalLabel">Diskusi Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Konten chat akan dimuat di sini oleh AJAX --}}
                    <div id="chat-content-container">
                        <p class="text-center">Memuat percakapan...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- Form untuk mengirim pesan akan dimuat di sini --}}
                    <div id="chat-form-container" style="width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // inisiasi tabel
    const table = $('#datatables').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route('kaprodi.submissions.index.datatable', ['type' => $type, 'status' => $status]) }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'txtReceiptNumber', name: 'txtReceiptNumber' },
            { data: 'user_full_name', name: 'user_full_name' },
            { data: 'letter_type', name: 'letter_type' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'dtmInserted', name: 'dtmInserted', defaultContent: '-' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });

    // Logika Modal Status
    let statusTable = null;

    // Event delegation untuk tombol status modal
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

    // Event delegation untuk tombol attachment modal
    $('#datatables tbody').on('click', '.show-attachment-modal', function(e) {
        e.preventDefault();

        const submissionId = $(this).data('submission-id');

        if (!submissionId) {
            console.error('Submission ID not found');
            return;
        }

        // Reset modal content
        $('#attachmentLoading').show();
        $('#attachmentList').hide();
        $('#attachmentError').hide();

        // Show modal
        $('#attachmentModal').modal('show');

        // Fetch attachments
        $.ajax({
            url: '{{ route("kaprodi.submissions.attachments", ":id") }}'.replace(':id', submissionId),
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#attachmentLoading').hide();

                if (response.success && response.data) {
                    const submission = response.data.submission;
                    const attachments = response.data.attachments;

                    // Set modal title and info
                    $('#attachmentModalLabel').text('Lampiran Dokumen - ' + submission.letter_type);
                    $('#attachmentSubmissionInfo').html(
                        '<strong>Pemohon:</strong> ' + submission.user_name + ' | ' +
                        '<strong>No. Pembuatan:</strong> ' + submission.receipt_number
                    );

                    if (attachments && attachments.length > 0) {
                        let attachmentHtml = '<div class="row">';

                        attachments.forEach(function(attachment, index) {
                            attachmentHtml += `
                                <div class="col-md-6 mb-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title text-truncate">${attachment.field_label}</h6>
                                            <p class="card-text text-muted small mb-3">${attachment.file_name}</p>

                                            <div class="bg-light border rounded p-3 mb-3 text-center d-flex align-items-center justify-content-center" style="min-height: 200px;">`;

                            if (attachment.is_image) {
                                attachmentHtml += `
                                    <img src="${attachment.file_url}"
                                         alt="${attachment.field_label}"
                                         class="img-fluid rounded shadow-sm"
                                         style="max-height: 180px; object-fit: contain; cursor: pointer;"
                                         onclick="previewFile('${attachment.file_url}', '${attachment.field_label}', 'image')">`;
                            } else if (attachment.is_pdf) {
                                attachmentHtml += `
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-file-pdf text-danger fs-1"></i>
                                        <p class="mt-2 mb-0 text-muted">PDF Document</p>
                                        <small class="text-muted">${attachment.file_name}</small>
                                    </div>`;
                            } else {
                                attachmentHtml += `
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-file text-secondary fs-1"></i>
                                        <p class="mt-2 mb-0 text-muted">File Document</p>
                                        <small class="text-muted">${attachment.file_name}</small>
                                    </div>`;
                            }

                            attachmentHtml += `
                                            </div>

                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-primary btn-sm flex-fill"
                                                        onclick="previewFile('${attachment.file_url}', '${attachment.field_label}', '${attachment.is_pdf ? 'pdf' : (attachment.is_image ? 'image' : 'other')}')">
                                                    <i class="fas fa-eye me-1"></i> Preview
                                                </button>
                                                <a href="${attachment.file_url}"
                                                   target="_blank"
                                                   class="btn btn-outline-primary btn-sm flex-fill"
                                                   download="${attachment.file_name}">
                                                    <i class="fas fa-download me-1"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                        });

                        attachmentHtml += '</div>';
                        $('#attachmentList').html(attachmentHtml).show();
                    } else {
                        $('#attachmentList').html(`
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open text-muted display-1"></i>
                                <p class="mt-3 text-muted fs-5">Tidak ada lampiran yang ditemukan</p>
                            </div>
                        `).show();
                    }
                } else {
                    $('#attachmentError').show();
                    $('#errorMessage').text(response.message || 'Gagal memuat lampiran');
                }
            },
            error: function(xhr, status, error) {
                $('#attachmentLoading').hide();
                $('#attachmentError').show();
                $('#errorMessage').text('Terjadi kesalahan saat memuat lampiran: ' + error);
            }
        });
    });

    // chat modal
    $('#datatables tbody').on('click', '.chat-btn', function() {
        var submissionId = $(this).data('id');
        var chatUrl = "{{ route('submissions.chat.index', ['submission' => '__ID__']) }}".replace('__ID__', submissionId);

        $('#chat-modal').attr('data-chat-url', chatUrl);
        $('#chat-content-container').html('<p class="text-center">Memuat percakapan...</p>');
        $('#chat-form-container').html('');
        $('#chat-modal').modal('show');

        // Ambil konten chat dari server
        $.get(chatUrl, function(response) {
            var contentHtml = $(response).filter('.chat-content-wrapper').html();
            var formHtml = $(response).filter('.chat-form-wrapper').html();

            // Suntikkan HTML ke dalam modal
            $('#chat-content-container').html(contentHtml);
            $('#chat-form-container').html(formHtml);

            var chatContent = document.querySelector('#chat-content-container .chat-content');
            chatContent.scrollTop = chatContent.scrollHeight;
        });
    });

    // form modal
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

// Function untuk preview file (tetap di luar document ready)
function previewFile(fileUrl, fileName, fileType) {
    $('#filePreviewModalLabel').text(fileName);
    $('#downloadFileLink').attr('href', fileUrl);

    let previewContent = '';

    if (fileType === 'image') {
        previewContent = `<img src="${fileUrl}" class="img-fluid rounded shadow" alt="${fileName}" style="max-height: 80vh;">`;
    } else if (fileType === 'pdf') {
        previewContent = `
            <embed src="${fileUrl}" type="application/pdf" width="100%" height="600px" class="rounded border-0">
            <div class="mt-3">
                <p class="text-muted">Jika PDF tidak tampil dengan baik, <a href="${fileUrl}" target="_blank" class="text-decoration-none">klik di sini untuk membuka di tab baru</a></p>
            </div>`;
    } else {
        previewContent = `
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <i class="fas fa-file text-muted display-1"></i>
                <p class="mt-3 text-muted fs-5">File ini tidak dapat di-preview</p>
                <p class="text-muted">Silakan download untuk melihat file</p>
            </div>`;
    }

    $('#filePreviewContent').html(previewContent);
    $('#filePreviewModal').modal('show');
}
</script>
@endpush
