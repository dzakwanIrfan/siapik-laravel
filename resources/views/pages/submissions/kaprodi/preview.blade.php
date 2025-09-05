@php($layoutNavbar = true)

@extends('layouts.default_layout')
@section('title', 'Preview Surat - Kaprodi')

@section('page_title')
  <!-- BEGIN breadcrumb -->
  <nav aria-label="breadcrumb" class="breadcrumb-header float-end float-lg-end">
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('kaprodi.submissions.index', ['type' => $type, 'status' => $status]) }}">Permintaan Surat</a></li>
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
                            src="{{ route('kaprodi.submissions.preview.html', $submission->intSubmission_ID) }}"
                            style="width: 100%; height: 800px; border: none;">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Persetujuan -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Tindakan Kaprodi</h5>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('kaprodi.submissions.process', $submission->intSubmission_ID) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Pilihan Tindakan:</h6>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="action" id="approve" value="approve" required>
                                <label class="form-check-label text-success fw-bold" for="approve">
                                    <i class="fas fa-check-circle me-2"></i>Setujui Pengajuan
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action" id="reject" value="reject" required>
                                <label class="form-check-label text-danger fw-bold" for="reject">
                                    <i class="fas fa-times-circle me-2"></i>Tolak Pengajuan
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="txtCatatan" class="form-label fw-bold">Catatan:</label>
                            <textarea name="txtCatatan" id="txtCatatan" class="form-control" rows="4"
                                    placeholder="Wajib diisi jika menolak..."></textarea>
                            <div class="form-text">Catatan ini akan disimpan di riwayat chat.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
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
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Parsley pada form
    const parsleyForm = $('form').parsley();

    // Elemen-elemen yang dibutuhkan
    const form = document.querySelector('form');
    const approveRadio = document.getElementById('approve');
    const rejectRadio = document.getElementById('reject');
    const noteTextarea = document.getElementById('txtCatatan');

    // Asumsikan Anda punya modal konfirmasi di HTML dengan ID #confirmModal
    const confirmModalEl = document.getElementById('confirmModal');
    const confirmModal = new bootstrap.Modal(confirmModalEl);

    // Fungsi untuk mengubah status 'required' pada catatan
    function handleActionChange() {
        if (rejectRadio.checked) {
            noteTextarea.setAttribute('required', 'required');
        } else {
            noteTextarea.removeAttribute('required');
        }
        // Validasi ulang dengan Parsley setelah mengubah aturan
        parsleyForm.validate();
    }

    // Pasang listener ke radio button
    approveRadio.addEventListener('change', handleActionChange);
    rejectRadio.addEventListener('change', handleActionChange);

    // Logika konfirmasi submit Anda
    form.addEventListener('submit', function(e) {
        // Hentikan submit hanya jika Parsley valid
        if (parsleyForm.isValid()) {
            e.preventDefault();

            const formData = new FormData(form);
            const action = formData.get('action');
            const note = formData.get('txtCatatan'); // Ambil dari txtCatatan

            if (!action) {
                alert('Silakan pilih tindakan (Setujui atau Tolak)');
                return;
            }

            const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
            const actionClass = action === 'approve' ? 'text-success' : 'text-danger';

            document.getElementById('confirmMessage').innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> Anda akan <span class="${actionClass} fw-bold">${actionText}</span> pengajuan ini.
                </div>
                ${note ? `<div class="mb-2"><strong>Catatan:</strong><br><em>"${note}"</em></div>` : ''}
                <p class="mb-0 small text-muted">Tindakan ini tidak dapat dibatalkan.</p>
            `;
            confirmModal.show();
        }
    });

    document.getElementById('confirmSubmit').addEventListener('click', function() {
        confirmModal.hide();
        form.submit(); // Lanjutkan submit form asli
    });
});
</script>
@endpush
