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
    <button class="btn btn-primary mb-3" id="btn-add-major">Tambah Prodi</button>
    <table class="table table-bordered" id="major-table" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Prodi</th>
                <th>Strata</th>
                <th>Gelar Sebutan</th>
                <th>Gelar Singkatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="major-modal" tabindex="-1" aria-labelledby="majorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="majorModalLabel">Form Prodi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="major-form" data-parsley-validate>
                <div class="modal-body">
                    <input type="hidden" id="major_id" name="major_id">
                    <div class="mb-3">
                        <label for="txtNameMajor" class="form-label">Nama Prodi</label>
                        <input type="text" class="form-control" id="txtNameMajor" name="txtNameMajor" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="txtStrata" class="form-label">Strata</label>
                            <input type="text" class="form-control" id="txtStrata" name="txtStrata" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="txtTitle" class="form-label">Gelar Sebutan</label>
                            <input type="text" class="form-control" id="txtTitle" name="txtTitle" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="txtShortTitle" class="form-label">Gelar Singkatan</label>
                            <input type="text" class="form-control" id="txtShortTitle" name="txtShortTitle" required>
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
{{-- Pastikan library ini sudah di-load di layout Mazer Anda --}}
<script>
$(document).ready(function() {
    // Setup CSRF Token
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    // Inisialisasi DataTables
    var table = $('#major-table').DataTable({
        processing: true,
        serverSide: true,
        // Gunakan URL saat ini (termasuk parameter) untuk AJAX
        ajax: '{{ route('prodi.data') }}' + window.location.search,
        columns: [
            { data: 'intMajor_ID', name: 'intMajor_ID' },
            { data: 'txtNameMajor', name: 'txtNameMajor' },
            { data: 'txtStrata', name: 'txtStrata' },
            { data: 'txtTitle', name: 'txtTitle' },
            { data: 'txtShortTitle', name: 'txtShortTitle' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Tombol Tambah: Buka Modal
    $('#btn-add-major').click(function() {
        $('#major-form').trigger("reset").parsley().reset();
        $('#major_id').val('');
        $('#majorModalLabel').text('Tambah Prodi Baru');
        $('#major-modal').modal('show');
    });

    // Tombol Edit: Ambil data & buka modal
    $('body').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $.get("{{ url('prodi') }}/" + id + '/edit', function(data) {
            $('#major-form').trigger("reset").parsley().reset();
            $('#majorModalLabel').text('Edit Prodi');
            $('#major_id').val(data.intMajor_ID);
            $('#txtNameMajor').val(data.txtNameMajor);
            $('#txtStrata').val(data.txtStrata);
            $('#txtTitle').val(data.txtTitle);
            $('#txtShortTitle').val(data.txtShortTitle);
            $('#major-modal').modal('show');
        });
    });

    // Tombol Simpan (Create & Update)
    $('#btn-save').click(function(e) {
        e.preventDefault();
        if ($('#major-form').parsley().validate()) {
            var id = $('#major_id').val();
            var url = id ? "{{ url('prodi') }}/" + id : "{{ route('prodi.store') }}";
            var method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $('#major-form').serialize(),
                success: function(response) {
                    $('#major-modal').modal('hide');
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
                    url: "{{ url('prodi') }}/" + id,
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
