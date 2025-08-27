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
    <h3>Manajemen Prodi</h3>
    <p class="text-subtitle text-muted">Welcome back 👋</p>
  </div>
  <!-- END page-header -->
@endsection

@section('content')
<div class="container mt-5">
    <button class="btn btn-primary mb-3" id="btn-add-requirement">Tambah Requirement</button>
    <table class="table table-bordered" id="requirement-table" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Requirement</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="requirement-modal" tabindex="-1" aria-labelledby="requirementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requirementModalLabel">Form Requirement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="requirement-form" data-parsley-validate>
                <div class="modal-body">
                    <input type="hidden" id="requirement_id" name="requirement_id">
                    <div class="mb-3">
                        <label for="txtNameRequirement" class="form-label">Nama Requirement</label>
                        <input type="text" class="form-control" id="txtNameRequirement" name="txtNameRequirement" required>
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
{{-- Pastikan library ini sudah di-load di layout Mazer Anda --}}
<script>
$(document).ready(function() {
    // Setup CSRF Token
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    // Inisialisasi DataTables
    var table = $('#requirement-table').DataTable({
        processing: true,
        serverSide: true,
        // Gunakan URL saat ini (termasuk parameter) untuk AJAX
        ajax: '{{ route('requirements.data') }}' + window.location.search,
        columns: [
            { data: 'intRequirement_ID', name: 'intRequirement_ID' },
            { data: 'txtNameRequirement', name: 'txtNameRequirement' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Tombol Tambah: Buka Modal
    $('#btn-add-requirement').click(function() {
        $('#requirement-form').trigger("reset").parsley().reset();
        $('#requirement_id').val('');
        $('#requirementModalLabel').text('Tambah Requirement Baru');
        $('#requirement-modal').modal('show');
    });

    // Tombol Edit: Ambil data & buka modal
    $('body').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $.get("{{ url('requirements') }}/" + id + '/edit', function(data) {
            $('#requirement-form').trigger("reset").parsley().reset();
            $('#requirementModalLabel').text('Edit Requirement');
            $('#requirement_id').val(data.intRequirement_ID);
            $('#txtNameRequirement').val(data.txtNameRequirement);
            $('#txtShortTitle').val(data.txtShortTitle);
            $('#requirement-modal').modal('show');
        });
    });

    // Tombol Simpan (Create & Update)
    $('#btn-save').click(function(e) {
        e.preventDefault();
        if ($('#requirement-form').parsley().validate()) {
            var id = $('#requirement_id').val();
            var url = id ? "{{ url('requirements') }}/" + id : "{{ route('requirements.store') }}";
            var method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $('#requirement-form').serialize(),
                success: function(response) {
                    $('#requirement-modal').modal('hide');
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

    // Tombol Hapus
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
                    url: "{{ url('requirements') }}/" + id,
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
