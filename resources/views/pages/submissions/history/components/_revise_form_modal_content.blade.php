<form id="reviseSubmissionForm" action="{{ route('submissions.update', $submission->intSubmission_ID) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="alert alert-info">
        Revisi pengajuan surat. Setelah disimpan, pengajuan akan dikirim ulang untuk ditinjau.
    </div>

    {{-- Loop melalui field dan panggil komponen dynamic-field --}}
    @foreach ($letterType->letterFields as $field)
        <x-dynamic-field
            :field="$field"
            :options="$fieldOptions"
            :currentValues="$currentValues" {{-- Kirim semua nilai saat ini --}}
        />
    @endforeach

    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="btn-save-revision">Simpan Revisi</button>
    </div>
</form>
