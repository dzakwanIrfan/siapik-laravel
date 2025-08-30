@php($layoutNavbar = true)

@extends('layouts.default_layout')
@section('title', 'Sistem Persuratan')

@section('page_title')
  <!-- BEGIN breadcrumb -->
  <nav aria-label="breadcrumb" class="breadcrumb-header float-end float-lg-end">
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
      </ol>
  </nav>
  <!-- END breadcrumb -->

  <!-- BEGIN page-header -->
  <div>
    <h3>Manajemen Jenis Surat</h3>
    <p class="text-subtitle text-muted">Welcome back 👋</p>
  </div>
  <!-- END page-header -->
@endsection

@section('content')
<div class="container mt-5">
    <h3>Manajemen Kolom Isian Surat</h3>
    <button class="btn btn-primary mb-3" id="btn-add">Tambah Kolom</button>
    <table class="table table-bordered" id="letter-fields-table" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Jenis Surat</th>
                <th>Nama Kolom</th>
                <th>Label</th>
                <th>Tipe</th>
                <th>Urutan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Form -->
<div class="modal fade" id="form-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Form Kolom Isian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="letter-field-form" data-parsley-validate>
                <div class="modal-body">
                    <input type="hidden" id="letter_field_id" name="letter_field_id">
                    <div class="mb-3">
                        <label for="intLetterType_ID" class="form-label">Jenis Surat</label>
                        <select class="form-select" id="intLetterType_ID" name="intLetterType_ID" required>
                            <option value="">Pilih Jenis Surat</option>
                            @foreach($letterTypes as $type)
                                <option value="{{ $type->intLetterType_ID }}">{{ $type->txtNameLetterType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="txtFieldName" class="form-label">Nama Kolom (tanpa spasi)</label>
                            <input type="text" class="form-control" id="txtFieldName" name="txtFieldName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="txtFieldLabel" class="form-label">Label Kolom</label>
                            <input type="text" class="form-control" id="txtFieldLabel" name="txtFieldLabel" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="txtFieldType" class="form-label">Tipe Kolom</label>
                            <select class="form-select" id="txtFieldType" name="txtFieldType" required>
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="select">Select</option>
                                <option value="date">Date</option>
                                <option value="file">File</option>
                                <option value="number">Number</option>
                            </select>
                        </div>
                         <div class="col-md-6 mb-3">
                            <label for="intFieldOrder" class="form-label">Urutan</label>
                            <input type="number" class="form-control" id="intFieldOrder" name="intFieldOrder" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bitRequired" class="form-label">Wajib Diisi</label>
                            <select class="form-select" id="bitRequired" name="bitRequired" required>
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bitActive" class="form-label">Status</label>
                            <select class="form-select" id="bitActive" name="bitActive" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    var table = $('#letter-fields-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("letter-fields.data") }}',
        columns: [
            { data: 'intLetterField_ID', name: 'intLetterField_ID' },
            { data: 'letter_type.txtNameLetterType', name: 'letterType.txtNameLetterType' },
            { data: 'txtFieldName', name: 'txtFieldName' },
            { data: 'txtFieldLabel', name: 'txtFieldLabel' },
            { data: 'txtFieldType', name: 'txtFieldType' },
            { data: 'intFieldOrder', name: 'intFieldOrder' },
            { data: 'bitActive', name: 'bitActive' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $('#btn-add').click(function() {
        $('#letter-field-form').trigger("reset").parsley().reset();
        $('#letter_field_id').val('');
        $('#formModalLabel').text('Tambah Kolom Isian Baru');
        $('#form-modal').modal('show');
    });

    $('body').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $.get("{{ url('letter-fields') }}/" + id + '/edit', function(data) {
            $('#letter-field-form').trigger("reset").parsley().reset();
            $('#formModalLabel').text('Edit Kolom Isian');
            $('#letter_field_id').val(data.intLetterField_ID);
            $('#intLetterType_ID').val(data.intLetterType_ID);
            $('#txtFieldName').val(data.txtFieldName);
            $('#txtFieldLabel').val(data.txtFieldLabel);
            $('#txtFieldType').val(data.txtFieldType);
            $('#bitRequired').val(data.bitRequired);
            $('#intFieldOrder').val(data.intFieldOrder);
            $('#bitActive').val(data.bitActive);
            $('#form-modal').modal('show');
        });
    });

    $('#btn-save').click(function(e) {
        e.preventDefault();
        if ($('#letter-field-form').parsley().validate()) {
            var id = $('#letter_field_id').val();
            var url = id ? "{{ url('letter-fields') }}/" + id : "{{ route('letter-fields.store') }}";
            var method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $('#letter-field-form').serialize(),
                success: function(response) {
                    $('#form-modal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Sukses!', response.success, 'success');
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var errorString = '';
                    $.each(errors, function(key, value) { errorString += '<li>' + value + '</li>'; });
                    Swal.fire('Error!', '<ul>' + errorString + '</ul>', 'error');
                }
            });
        }
    });

    $('body').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('letter-fields') }}/" + id,
                    success: function(response) {
                        table.ajax.reload();
                        Swal.fire('Dihapus!', response.success, 'success');
                    }
                });
            }
        });
    });
});
</script>
@endpush
