@php($layoutNavbar = true)

@extends('layouts.default_layout')
@section('title', 'Preview Surat - Akademik')

@section('page_title')
  <!-- BEGIN breadcrumb -->
  <nav aria-label="breadcrumb" class="breadcrumb-header float-end float-lg-end">
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('akademik.submissions.index', ['type' => $type, 'status' => $status]) }}">Permintaan Surat</a></li>
          <li class="breadcrumb-item active" aria-current="page">Preview Surat</li>
      </ol>
  </nav>
  <!-- END breadcrumb -->

  <!-- BEGIN page-header -->
  <div>
    <h3>Preview dan Persetujuan Surat</h3>
    <p class="text-subtitle text-muted">{{ $submission->letterType->txtNameLetterType ?? 'Jenis Surat' }}</p>
  </div>
  <!-- END page-header -->
@endsection

@section('content')
<div id="status-container" data-status="{{ $submission->txtStatus }}"></div>

<div class="row">
    <!-- Informasi Pengajuan -->
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-header bg-primary">
                <h5 class="mb-0 text-white">Informasi Pengajuan</h5>
            </div>
            <div class="card-body pb-0">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="200" class="fw-bold">No. Pembuatan</td>
                                <td>: {{ $submission->txtReceiptNumber }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Jenis Surat</td>
                                <td>: {{ $submission->letterType->txtNameLetterType }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status</td>
                                <td>: <span class="badge bg-warning text-dark">{{ $submission->txtStatus }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="200" class="fw-bold">Nama Pemohon</td>
                                <td>: {{ $submission->user->txtFullName }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">NIM</td>
                                <td>: {{ $submission->user->mahasiswaProfile->txtNIM ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Waktu Pengajuan</td>
                                <td>: {{ date('d F Y H:i', strtotime($submission->dtmInserted)) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Surat -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary">
                <h5 class="mb-0 text-white">Preview Surat</h5>
            </div>
            <div class="card-body p-3">
                <div id="letter-preview" class="letter-container border rounded shadow-sm" style="background: white;">
                    <iframe id="letterFrame"
                            src="{{ route('akademik.submissions.preview.html', $submission->intSubmission_ID) }}"
                            style="width: 100%; height: 800px; border: none;">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    @if (!in_array($submission->txtStatus, ['Sedang ditinjau Kaprodi', 'Disetujui Kaprodi','Ditolak Kaprodi']))
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="mb-0 text-white">Cetak & Konfirmasi Surat</h5>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button"
                                class="btn btn-warning print-letter-btn"
                                data-submission-id="{{ $submission->intSubmission_ID }}">
                            <i class="fas fa-print me-2"></i>Cetak Surat
                        </button>
                        {{-- <button type="button"
                                class="btn btn-success download-letter-btn"
                                data-submission-id="{{ $submission->intSubmission_ID }}">
                            <i class="fas fa-download me-2"></i>Download PDF
                        </button> --}}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(in_array($submission->txtStatus, ['Sudah dicetak', 'Selesai']))
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0 text-white">Upload Surat Final (TTD)</h5>
                </div>
                <div class="card-body py-4">
                    <div id="final-letter-display">
                        @if($submission->txtStatus === 'Selesai')
                            <div class="alert alert-success">
                                <h6 class="alert-heading">Surat Final Sudah Terupload</h6>
                                <a href="{{ asset('storage/' . $submission->txtFinalFile) }}" target="_blank" class="btn btn-primary mt-2">
                                    <i class="fas fa-eye me-2"></i>Lihat Surat Final
                                </a>
                            </div>
                        @endif
                    </div>
                    <form action="{{ route('akademik.submissions.uploadFinal', $submission->intSubmission_ID) }}" id="upload-final-form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="final_letter_file" class="form-label fw-bold">Pilih File Surat (PDF):</label>
                            <input class="form-control" type="file" id="final_letter_file" name="final_letter_file" accept=".pdf" required>
                            <div class="form-text">Upload file surat yang sudah ditandatangani.</div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success" id="btn-upload-final">
                                <i class="fas fa-upload me-2"></i>Unggah File
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Persetujuan -->
    {{-- @if($submission->txtStatus === 'Disetujui Kaprodi') --}}
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Tindakan akademik</h5>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('akademik.submissions.process', $submission->intSubmission_ID) }}" id="approval-form" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Pilihan Tindakan:</h6>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="action" id="approve" value="approve" required {{ $submission->txtStatus === 'Disetujui Akademik' ? 'checked' : '' }}>
                                <label class="form-check-label text-success fw-bold" for="approve">
                                    <i class="fas fa-check-circle me-2"></i>Setujui Pengajuan
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action" id="reject" value="reject" required {{ $submission->txtStatus === 'Ditolak Akademik' ? 'checked' : '' }}>
                                <label class="form-check-label text-danger fw-bold" for="reject">
                                    <i class="fas fa-times-circle me-2"></i>Tolak Pengajuan
                                </label>
                            </div>
                        </div>

                        <div class="mb-4 d-none" id="letterNumberContainer">
                            <label for="txtLetterNumber" class="form-label fw-bold">Nomor Surat (Wajib):</label>
                            <input name="txtLetterNumber" id="txtLetterNumber" class="form-control" placeholder="Masukkan nomor surat yang sesuai..." value="{{ old('txtLetterNumber', $submission->txtLetterNumber) }}">
                            <div class="form-text">Masukan nomor surat yang sesuai.</div>
                        </div>

                        <div class="mb-4 d-none" id="noteContainer">
                            <label for="txtCatatan" class="form-label fw-bold">Catatan:</label>
                            <textarea name="txtCatatan" id="txtCatatan" class="form-control" rows="4"
                                    placeholder="Wajib diisi jika menolak..."></textarea>
                            <div class="form-text">Catatan ini akan disimpan di riwayat chat.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" id="btn-process-submission" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Proses Pengajuan
                            </button>
                            <a href="{{ route('akademik.submissions.index', ['type' => $type, 'status' => $status]) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Lampiran Dokumen</h5>
                </div>
                <div class="card-body py-2">
                    @if($attachments->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($attachments as $attachment)
                                @if($attachment->txtFieldValue && file_exists(storage_path('app/public/' . $attachment->txtFieldValue)))
                                    <div class="list-group-item p-0">
                                        <div class="d-flex justify-content-between align-items-center py-2 gap-5">
                                            <div class="flex-fill">
                                                <small class="text-muted fw-bold">{{ $attachment->letterField->txtFieldLabel ?? $attachment->txtFieldLabel }}</small><br>
                                            </div>
                                            <a href="{{ asset('storage/' . $attachment->txtFieldValue) }}"
                                            target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Tidak ada lampiran</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- @endif --}}
</div>

{{-- Modal Konfirmasi Print/Download --}}
<div class="modal fade" id="printDownloadModal" tabindex="-1" aria-labelledby="printDownloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printDownloadModalLabel">Konfirmasi Aksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong>
                    <span id="printDownloadMessage"></span>
                </div>
                <div class="mb-3">
                    <strong>Pemohon:</strong> {{ $submission->user->txtFullName }}<br>
                    <strong>Jenis Surat:</strong> {{ $submission->letterType->txtNameLetterType }}<br>
                    <strong>No. Pembuatan:</strong> {{ $submission->txtReceiptNumber }}
                </div>
                <p class="mb-0 small text-muted">
                    Setelah aksi ini, status akan berubah menjadi "Sudah dicetak" dengan proses "TTD Basah".
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmPrintDownload">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.querySelector('#approval-form');
    const approveRadio = document.getElementById('approve');
    const rejectRadio  = document.getElementById('reject');
    const letterNumberContainer = document.getElementById('letterNumberContainer');
    const letterNumberInput = document.getElementById('txtLetterNumber');
    const noteContainer = document.getElementById('noteContainer');
    const noteTextarea = document.getElementById('txtCatatan');

    // Handle print/download buttons (tetap sama)
    let currentAction = null;
    let currentUrl = null;

    // Print button handler
    document.querySelectorAll('.print-letter-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const submissionId = this.getAttribute('data-submission-id');
            currentAction = 'print';
            currentUrl = `{{ url('/akademik/submissions') }}/${submissionId}/print`;

            document.getElementById('printDownloadMessage').textContent =
                'Anda akan membuka halaman print surat dan mengubah status menjadi "Sudah dicetak".';

            const modal = new bootstrap.Modal(document.getElementById('printDownloadModal'));
            modal.show();
        });
    });

    // Download button handler
    document.querySelectorAll('.download-letter-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const submissionId = this.getAttribute('data-submission-id');
            currentAction = 'download';
            currentUrl = `{{ url('/akademik/submissions') }}/${submissionId}/download`;

            document.getElementById('printDownloadMessage').textContent =
                'Anda akan mendownload surat dalam format PDF dan mengubah status menjadi "Sudah dicetak".';

            const modal = new bootstrap.Modal(document.getElementById('printDownloadModal'));
            modal.show();
        });
    });

    // Confirm print/download action
    document.getElementById('confirmPrintDownload').addEventListener('click', function() {
        if (currentUrl) {
            if (currentAction === 'print') {
                window.open(currentUrl, '_blank');
            } else if (currentAction === 'download') {
                window.location.href = currentUrl;
            }

            const modal = bootstrap.Modal.getInstance(document.getElementById('printDownloadModal'));
            modal.hide();

            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    });

    // Form approval logic (jika form ada)
    if (form && approveRadio && rejectRadio) {
        function syncLetterNumberVisibility() {
            const show = approveRadio.checked;
            letterNumberContainer.classList.toggle('d-none', !show);
            if (show) {
                letterNumberInput.setAttribute('required', 'required');
            } else {
                letterNumberInput.removeAttribute('required');
                letterNumberInput.value = '';
            }
        }

        function syncNoteVisibility() {
            const show = rejectRadio.checked;
            noteContainer.classList.toggle('d-none', !show);
            if (show) {
                noteTextarea.setAttribute('required', 'required');
            } else {
                noteTextarea.removeAttribute('required');
            }
        }

        // Event listeners untuk radio buttons
        approveRadio.addEventListener('change', function() {
            syncLetterNumberVisibility();
            syncNoteVisibility();
        });
        rejectRadio.addEventListener('change', function() {
            syncLetterNumberVisibility();
            syncNoteVisibility();
        });

        // Set awal
        syncLetterNumberVisibility();
        syncNoteVisibility();

        // Form submit dengan konfirmasi
        $('#btn-process-submission').on('click', function(e) {
            e.preventDefault();

            const actionRadio = $('input[name="action"]:checked');
            const noteTextarea = $('#txtCatatan');
            const letterNumberInput = $('#txtLetterNumber');

            // Validasi client-side
            if (actionRadio.length === 0) {
                Swal.fire('Peringatan', 'Silakan pilih tindakan (Setujui atau Tolak)', 'warning');
                return;
            }
            if (actionRadio.val() === 'approve' && letterNumberInput.val().trim() === '') {
                Swal.fire('Peringatan', 'Nomor surat wajib diisi saat menyetujui pengajuan.', 'warning');
                letterNumberInput.focus();
                return;
            }
            if (actionRadio.val() === 'reject' && noteTextarea.val().trim() === '') {
                Swal.fire('Peringatan', 'Catatan wajib diisi saat menolak pengajuan.', 'warning');
                noteTextarea.focus();
                return;
            }

            const actionText = actionRadio.val() === 'approve' ? 'menyetujui' : 'menolak';

            // Konfirmasi dengan SweetAlert
            Swal.fire({
                title: 'Anda Yakin?',
                text: `Anda akan ${actionText} pengajuan surat ini. Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form secara normal (bukan AJAX)
                    form.submit();
                }
            });
        });
    }

    // Upload final form (tetap pakai AJAX karena lebih praktis untuk upload file)
    $('#upload-final-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action');
        const submitBtn = $('#btn-upload-final');
        const originalBtnText = submitBtn.html();
        const formData = new FormData(this);

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Mengunggah...');
            },
            success: function(response) {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message });

                const newFileHtml = `
                    <div class="alert alert-success">
                        <h6 class="alert-heading">Surat Final Berhasil Diunggah</h6>
                        <a href="${response.file_url}" target="_blank" class="btn btn-primary mt-2">
                            <i class="fas fa-eye me-2"></i>Lihat Surat Final
                        </a>
                    </div>`;

                $('#final-letter-display').html(newFileHtml);
                form.find('#final_letter_file').val('');
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan.';
                if (xhr.status === 422) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                Swal.fire({ icon: 'error', title: 'Gagal!', text: errorMsg });
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
});
</script>
@endpush
