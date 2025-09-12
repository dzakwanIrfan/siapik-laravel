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

                        <div class="mb-4 d-none" id="noteContainer">
                            <label for="txtCatatan" class="form-label fw-bold">Catatan:</label>
                            <textarea name="txtCatatan" id="txtCatatan" class="form-control" rows="4"
                                    placeholder="Wajib diisi jika menolak..."></textarea>
                            <div class="form-text">Catatan ini akan disimpan di riwayat chat.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="click" id="btnProsesPengajuan" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Proses Pengajuan
                            </button>
                            <a href="{{ route('kaprodi.submissions.index', ['type' => $type, 'status' => 'proses']) }}" class="btn btn-outline-secondary">
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
    const noteContainer = document.getElementById('noteContainer');
    const prosesBtn = document.getElementById('btnProsesPengajuan');

    // Fungsi untuk mengubah status 'required' pada catatan
    function handleActionChange() {
        if (rejectRadio.checked) {
            noteContainer.classList.remove('d-none');
            noteTextarea.setAttribute('required', 'required');
        } else {
            // Jika "Setujui" dipilih:
            noteContainer.classList.add('d-none');
            noteTextarea.removeAttribute('required');
            noteTextarea.value = '';
        }

        if (typeof parsleyForm !== 'undefined') {
            parsleyForm.validate();
        }
    }

    approveRadio.addEventListener('change', handleActionChange);
    rejectRadio.addEventListener('change', handleActionChange);

    handleActionChange();

    prosesBtn.addEventListener('click', function() {
        const selectedAction = document.querySelector('input[name="action"]:checked');

        if (!selectedAction) {
            Swal.fire({
                title: 'Tindakan Diperlukan',
                text: 'Anda harus memilih "Setujui" atau "Tolak" sebelum melanjutkan.',
                icon: 'warning',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'Baik, Saya Mengerti'
            });
            return;
        }

        if (!parsleyForm.validate()) {
            return;
        }
    });

    document.getElementById('confirmSubmit').addEventListener('click', function() {

        prosesBtn.disabled = true;
        prosesBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...`;

        form.submit();
    });
});
</script>
@endpush
