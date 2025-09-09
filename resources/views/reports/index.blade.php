@php($layoutNavbar = true)

@extends('layouts.default_layout')
@section('title', 'Laporan Pengajuan - Sistem Persuratan')

@section('page_title')
    <div>
        <h3>Laporan Pengajuan Surat</h3>
        <p class="text-subtitle text-muted">Analisis dan ekspor data pengajuan surat.</p>
    </div>
@endsection

@section('content')
    {{-- FORM FILTER --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Filter Laporan</h4>
        </div>
        <div class="card-body">
            <form id="filter-form" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="end_date" name="end_date">
                </div>
                <div class="col-md-3">
                    <label for="major_id" class="form-label">Program Studi</label>
                    <select id="major_id" name="major_id" class="form-select">
                        <option value="">Semua Prodi</option>
                        @foreach($majors as $major)
                            <option value="{{ $major->intMajor_ID }}">{{ $major->txtNameMajor }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="letter_type_id" class="form-label">Jenis Surat</label>
                    <select id="letter_type_id" name="letter_type_id" class="form-select">
                        <option value="">Semua Jenis</option>
                         @foreach($letterTypes as $type)
                            <option value="{{ $type->intLetterType_ID }}">{{ $type->txtNameLetterType }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2">
                    <button type="button" id="btn-reset" class="btn btn-light">Reset</button>
                    <button type="submit" id="btn-filter" class="btn btn-primary">Terapkan Filter</button>
                    <button type="button" id="btn-export" class="btn btn-success">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Detail Data Laporan</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered" id="reports-table" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Resi</th>
                        <th>Nama Pemohon</th>
                        <th>Prodi</th>
                        <th>Jenis Surat</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#reports-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('reports.submissions.data') }}",
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.major_id = $('#major_id').val();
                d.letter_type_id = $('#letter_type_id').val();
                d.status = $('#status').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'txtReceiptNumber', name: 'txtReceiptNumber' },
            { data: 'user_name', name: 'user.txtFullName' },
            { data: 'major_name', name: 'user.mahasiswaProfile.major.txtNameMajor' },
            { data: 'letter_type_name', name: 'letterType.txtNameLetterType' },
            { data: 'txtStatus', name: 'txtStatus' },
            { data: 'date', name: 'dtmInserted' }
        ],
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });

    // Event handler for form filter
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();

        // 1. Store references to the button and its original HTMLF
        const filterButton = $('#btn-filter');
        const originalButtonHtml = filterButton.html();

        // 2. Manually disable the button and show a spinner
        filterButton.prop('disabled', true).html(
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyaring...`
        );

        // 3. Reload the table and use the callback function
        table.ajax.reload(function() {
            // 4. This code runs AFTER the data has been reloaded successfully
            // Re-enable the button and restore its original content
            filterButton.prop('disabled', false).html(originalButtonHtml);
        });
    });

    // Event handler for tombol reset
    $('#btn-reset').on('click', function() {
        $('#filter-form')[0].reset();
        // Trigger the submit event to reload with the loading spinner
        $('#filter-form').submit();
    });

    $('#btn-export').on('click', function() {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const majorId = $('#major_id').val();
        const letterTypeId = $('#letter_type_id').val();
        const status = $('#status').val();

        const exportUrl = new URL("{{ route('reports.submissions.export') }}");
        exportUrl.searchParams.append('start_date', startDate);
        exportUrl.searchParams.append('end_date', endDate);
        exportUrl.searchParams.append('major_id', majorId);
        exportUrl.searchParams.append('letter_type_id', letterTypeId);
        exportUrl.searchParams.append('status', status);

        window.open(exportUrl.href, '_blank');
    });
});
</script>
@endpush
