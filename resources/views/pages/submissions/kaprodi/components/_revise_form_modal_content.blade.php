<form id="reviseSubmissionForm" action="{{ route('kaprodi.submissions.update.revision', $submission->intSubmission_ID) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Revisi Pengajuan Surat</strong><br>
        Setelah disimpan, pengajuan akan dikirim ulang untuk ditinjau. Untuk file, Anda dapat mengganti dengan file baru atau membiarkan file lama tetap digunakan.
    </div>

    {{-- Loop melalui field dan panggil komponen dynamic-field dengan flag isRevision --}}
    @foreach ($letterType->letterFields as $field)
        <x-dynamic-field
            :field="$field"
            :options="$fieldOptions"
            :currentValues="$currentValues"
            :isRevision="true"
        />
    @endforeach

    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Batal
        </button>
        <button type="submit" class="btn btn-primary" id="btn-save-revision">
            <i class="fas fa-save me-1"></i> Simpan Revisi
        </button>
    </div>
</form>
