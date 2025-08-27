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
    <h3>Manajemen Konsentrasi</h3>
    <p class="text-subtitle text-muted">Welcome back 👋</p>
  </div>
  <!-- END page-header -->
@endsection

@section('content')
<div class="container mt-5">
    <h3>Manajemen Peminatan</h3>
    <button class="btn btn-primary mb-3" id="btn-add-concentrate">Tambah Peminatan</button>
    <table class="table table-bordered" id="concentrates-table" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Prodi</th>
                <th>Nama Peminatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Form -->
<div class="modal fade" id="concentrate-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="concentrateModalLabel">Form Peminatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="concentrate-form" data-parsley-validate>
                <div class="modal-body">
                    <input type="hidden" id="concentrate_id" name="concentrate_id">
                    <div class="mb-3">
                        <label for="intMajor_ID" class="form-label">Prodi</label>
                        <select class="form-select" id="intMajor_ID" name="intMajor_ID" required>
                            <option value="">Pilih Prodi</option>
                            @foreach($majors as $major)
                                <option value="{{ $major->intMajor_ID }}">{{ $major->txtNameMajor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="txtNameConcentrate" class="form-label">Nama Peminatan</label>
                        <input type="text" class="form-control" id="txtNameConcentrate" name="txtNameConcentrate" required>
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
    // Setup CSRF Token
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    // Inisialisasi DataTables
    var table = $('#concentrates-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('concentrates.data') }}',
        columns: [
            { data: 'intConcentrate_ID', name: 'intConcentrate_ID' },
            { data: 'major.txtNameMajor', name: 'major.txtNameMajor' }, // Ambil dari relasi
            { data: 'txtNameConcentrate', name: 'txtNameConcentrate' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
    });

    // Tombol Tambah
    $('#btn-add-concentrate').click(function() {
        $('#concentrate-form').trigger("reset").parsley().reset();
        $('#concentrate_id').val('');
        $('#concentrateModalLabel').text('Tambah Peminatan Baru');
        $('#concentrate-modal').modal('show');
    });

    // Tombol Edit
    $('body').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $.get("{{ url('concentrates') }}/" + id + '/edit', function(data) {
            $('#concentrate-form').trigger("reset").parsley().reset();
            $('#concentrateModalLabel').text('Edit Peminatan');
            $('#concentrate_id').val(data.intConcentrate_ID);
            $('#intMajor_ID').val(data.intMajor_ID);
            $('#txtNameConcentrate').val(data.txtNameConcentrate);
            $('#concentrate-modal').modal('show');
        });
    });

    // Tombol Simpan
    $('#btn-save').click(function(e) {
        e.preventDefault();
        if ($('#concentrate-form').parsley().validate()) {
            var id = $('#concentrate_id').val();
            var url = id ? "{{ url('concentrates') }}/" + id : "{{ route('concentrates.store') }}";
            var method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $('#concentrate-form').serialize(),
                success: function(response) {
                    $('#concentrate-modal').modal('hide');
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
                    url: "{{ url('concentrates') }}/" + id,
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
