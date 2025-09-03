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
        ajax: '{{ route('akademik.submissions.index.datatable', ['type' => $type, 'status' => $status]) }}',
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

    // Event delegation untuk tombol status modal
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

    // Event delegation untuk tombol attachment modal
    $(document).on('click', '.show-attachment-modal', function(e) {
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
            url: '{{ route("akademik.submissions.attachments", ":id") }}'.replace(':id', submissionId),
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
});

// Function untuk preview file
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

@if($status === 'proses')
<script>
    window.initSelect2 = function(scope) {
    if (typeof Choices !== 'undefined') {
        scope.querySelectorAll('.default-select2, select[data-choices], .use-choices').forEach(function (el) {
        if (el._choices) return; // hindari double init
        try {
            const isMultiple = !!el.multiple;
            const placeholder = el.getAttribute('data-placeholder') || 'Pilih...';
            el._choices = new Choices(el, {
            shouldSort: false,
            searchEnabled: true,
            placeholder: true,
            placeholderValue: placeholder,
            removeItemButton: isMultiple, // tombol hapus untuk multi-select
            itemSelectText: '',
            position: 'auto'
            });
        } catch (e) { /* noop */ }
        });
        return;
    }

    // ====== Fallback ke Select2 (kalau memang ada) ======
    if (window.jQuery && jQuery.fn.select2) {
        jQuery(scope).find('.default-select2').each(function () {
        if (jQuery(this).data('select2')) return;
        jQuery(this).select2({
            width: '100%',
            dropdownParent: jQuery('#editSubmissionModal') // untuk modal edit
        });
        });
    }
    };

    window.initFlatpickr = function(scope) {
    if (window.flatpickr) {
        scope.querySelectorAll('.flatpickr-input').forEach(function (el) {
        if (!el._fp) {
            flatpickr(el, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true
            });
        }
        });
    }
    };

    window.initFilePond = function(scope) {
    if (window.FilePond) {
        try {
        if (window.FilePondPluginImagePreview) FilePond.registerPlugin(FilePondPluginImagePreview);
        if (window.FilePondPluginFileValidateType) FilePond.registerPlugin(FilePondPluginFileValidateType);
        } catch(e){}
                scope.querySelectorAll('input[type="file"].filepond').forEach(function (el) {
        if (!el._pond) {
            const pond = FilePond.create(el, {
            allowMultiple: false,
            credits: false,
            storeAsFile: true
            });
            el._pond = pond;
        }
        });
    }
    };

    // ========== EDIT SUBMISSION MODAL ==========
    $(document).ready(function() {
        // Modal Edit Submission - buat secara dinamis
        const editModalHtml = `
        <div class="modal fade" id="editSubmissionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                <div>
                    <h5 class="modal-title mb-0">Edit Data Submission</h5>
                    <small class="text-muted">Jenis: <span id="editModalLetterName" class="fw-medium"></span></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                <form id="editDynamicForm" method="POST" enctype="multipart/form-data" data-parsley-validate>
                    <div id="editDynamicFields"><!-- akan diisi via AJAX --></div>
                </form>
                </div>

                <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnEditSubmit">Update Data</button>
                </div>
            </div>
            </div>
        </div>`;

        // Append modal ke body jika belum ada
        if (!document.getElementById('editSubmissionModal')) {
            $('body').append(editModalHtml);
        }
    });

    // Parsley init untuk form edit
    function initEditParsley() {
        if (!(window.jQuery && jQuery.fn.parsley)) return;
        const $editForm = jQuery('#editDynamicForm');
        try { $editForm.parsley().destroy(); } catch (e) {}

        $editForm.parsley({
            trigger: 'change',
            errorClass: 'is-invalid',
            successClass: 'is-valid',
            errorsWrapper: '<div class="invalid-feedback"></div>',
            errorTemplate: '<span></span>',
            classHandler: function (field) {
                const $el = field.$element;

                // Choices.js
                const $choicesWrap = $el.closest('.choices');
                if ($choicesWrap.length) return $choicesWrap;

                // Select2 (fallback)
                if ($el.hasClass('select2-hidden-accessible')) {
                    return $el.next('.select2').find('.select2-selection');
                }

                // FilePond
                if ($el.hasClass('filepond') && $el.get(0)?._pond) {
                    return jQuery($el.get(0)._pond.element);
                }

                return $el;
            },
            errorsContainer: function (field) {
                const $el = field.$element;
                return $el.closest('.form-group').length ? $el.closest('.form-group') : $el.parent();
            }
        });
    }

    // Event delegation untuk tombol edit
    $(document).on('click', '.btn-open-letter', function(e) {
        e.preventDefault();

        const submissionId = $(this).data('submission-id');
        const editForm = document.getElementById('editDynamicForm');
        const editWrap = document.getElementById('editDynamicFields');
        const editSubmitBtn = document.getElementById('btnEditSubmit');

        if (!submissionId) {
            console.error('Submission ID not found');
            return;
        }

        editWrap.innerHTML = '<div class="text-center py-5"><div class="spinner-border" role="status"></div><div class="mt-2">Memuat formulir...</div></div>';

        // Set form action URL
        editForm.action = `{{ url('/akademik/submissions') }}/${submissionId}/update`;

        // Fetch form content
        fetch(`{{ url('/akademik/submissions') }}/${submissionId}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            editWrap.innerHTML = html;

            // Init enhancers untuk form edit menggunakan fungsi global
            try {
                window.initSelect2(editWrap);
                window.initFlatpickr(editWrap);
                // Tidak perlu initFilePond karena tidak ada field file
                initEditParsley();
            } catch (error) {
                console.error('Error initializing form enhancers:', error);
            }

            // Show modal
            $('#editSubmissionModal').modal('show');
        })
        .catch(error => {
            console.error('Error:', error);
            editWrap.innerHTML = '<div class="alert alert-danger">Gagal memuat formulir edit.</div>';
        });
    });

    // Submit handler untuk edit form
    $(document).on('click', '#btnEditSubmit', function() {
        const btn = this;
        const editForm = document.getElementById('editDynamicForm');
        const Toast = (typeof Swal !== 'undefined')
            ? Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (t) => {
                    t.onmouseenter = Swal.stopTimer;
                    t.onmouseleave = Swal.resumeTimer;
                }
            })
            : null;

        let valid = true;
        if (window.jQuery && jQuery.fn.parsley) {
            valid = jQuery(editForm).parsley().validate();
        } else {
            valid = editForm.checkValidity();
            if (!valid) editForm.reportValidity();
        }

        if (valid) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

            const formData = new FormData(editForm);

            fetch(editForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#editSubmissionModal').modal('hide');
                    if (Toast) {
                        Toast.fire({
                            icon: "success",
                            title: data.message || "Data berhasil diperbarui"
                        });
                    }
                    // Reload DataTable
                    if (typeof table !== 'undefined') {
                        table.ajax.reload(null, false); // reload tanpa reset paging
                    }
                } else {
                    if (Toast) {
                        Toast.fire({
                            icon: "error",
                            title: data.message || "Gagal memperbarui data"
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (Toast) {
                    Toast.fire({
                        icon: "error",
                        title: "Terjadi kesalahan sistem"
                    });
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Update Data';
            });
        } else {
            if (Toast) {
                Toast.fire({
                    icon: "error",
                    title: "Data tidak valid/lengkap"
                });
            }
        }
    });

    // Reset validasi saat modal edit dibuka
    $(document).on('shown.bs.modal', '#editSubmissionModal', function () {
        if (window.jQuery && jQuery.fn.parsley) {
            jQuery('#editDynamicForm').parsley().reset();
        }
    });

    // Cleanup Choices.js saat modal ditutup untuk menghindari memory leak
    $(document).on('hidden.bs.modal', '#editSubmissionModal', function () {
        const editWrap = document.getElementById('editDynamicFields');
        if (editWrap) {
            // Destroy Choices.js instances
            editWrap.querySelectorAll('select').forEach(function(select) {
                if (select._choices) {
                    try {
                        select._choices.destroy();
                        select._choices = null;
                    } catch(e) {
                        console.warn('Error destroying Choices.js:', e);
                    }
                }
            });

            // Destroy FilePond instances
            editWrap.querySelectorAll('.filepond').forEach(function(input) {
                if (input._pond) {
                    try {
                        input._pond.destroy();
                        input._pond = null;
                    } catch(e) {
                        console.warn('Error destroying FilePond:', e);
                    }
                }
            });

            // Destroy Flatpickr instances
            editWrap.querySelectorAll('.flatpickr-input').forEach(function(input) {
                if (input._fp) {
                    try {
                        input._fp.destroy();
                        input._fp = null;
                    } catch(e) {
                        console.warn('Error destroying Flatpickr:', e);
                    }
                }
            });
        }
    });
</script>
@endif
@endpush
