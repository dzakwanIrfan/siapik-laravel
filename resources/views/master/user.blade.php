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
    <h3>Manajemen Users</h3>
    <p class="text-subtitle text-muted">Welcome back 👋</p>
  </div>
  <!-- END page-header -->
@endsection

@section('content')
<div class="container mt-5">
    <button class="btn btn-primary mb-3" id="btn-add-user">Tambah User</button>
    <table class="table table-bordered" id="users-table" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                {{-- <th>NIM</th> --}}
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="user-modal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Form User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="user-form" data-parsley-validate>
                    <input type="hidden" id="user_id" name="user_id">
                    <div class="mb-3">
                        <label for="txtFullName" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="txtFullName" name="txtFullName" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="txtEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="txtEmail" name="txtEmail" required data-parsley-type="email">
                        </div>
                    </div>

                    {{-- ====================================================== --}}
                    {{-- BAGIAN YANG DITAMBAHKAN UNTUK MEMENUHI SKEMA DATABASE --}}
                    {{-- ====================================================== --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="txtBirthPlace" class="form-label">Tempat Lahir</label>
                            <input type="text" class="form-control" id="txtBirthPlace" name="txtBirthPlace" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dtmBirthDate" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="dtmBirthDate" name="dtmBirthDate" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="txtPhone" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="txtPhone" name="txtPhone">
                        </div>
                        {{-- <div class="col-md-6 mb-3">
                            <label for="txtYear" class="form-label">Tahun Angkatan</label>
                            <input type="text" class="form-control" id="txtYear" name="txtYear">
                        </div> --}}
                    </div>
                    {{-- ====================================================== --}}
                    {{-- AKHIR DARI BAGIAN YANG DITAMBAHKAN --}}
                    {{-- ====================================================== --}}

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="txtGender" class="form-label">Jenis Kelamin</label>
                            <select class="form-select" id="txtGender" name="txtGender" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                @foreach($roles as $roleName)
                                    <option value="{{ $roleName }}">{{ ucfirst($roleName) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- ====================================================== --}}
                    {{-- FORM TAMBAHAN UNTUK PROFIL MAHASISWA (SEMBUNYI DEFAULT) --}}
                    {{-- ====================================================== --}}
                    <div id="mahasiswa-fields" style="display: none;">
                        <hr>
                        <h5>Profil Mahasiswa</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="txtNIM" class="form-label">NIM</label>
                                <input type="text" class="form-control" id="txtNIM" name="txtNIM">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="txtYear" class="form-label">Tahun Angkatan</label>
                                <input type="text" class="form-control" id="txtYear" name="txtYear">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="intMajor_ID" class="form-label">Prodi</label>
                                <select class="form-select" id="intMajor_ID" name="intMajor_ID">
                                    <option value="">Pilih Prodi</option>
                                    @foreach($majors as $major)
                                        <option value="{{ $major->intMajor_ID }}">{{ $major->txtNameMajor }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="intConcentrate_ID" class="form-label">Peminatan</label>
                                <select class="form-select" id="intConcentrate_ID" name="intConcentrate_ID">
                                    <option value="">Pilih Prodi terlebih dahulu</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- ====================================================== --}}
                    {{-- FORM TAMBAHAN UNTUK PROFIL DOSEN (SEMBUNYI DEFAULT) --}}
                    {{-- ====================================================== --}}
                    <div id="dosen-fields" style="display: none;">
                        <hr>
                        <h5>Profil Dosen</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="txtNIP" class="form-label">NIP</label>
                                <input type="text" class="form-control" id="txtNIP" name="txtNIP">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="txtNIDN" class="form-label">NIDN</label>
                                <input type="text" class="form-control" id="txtNIDN" name="txtNIDN">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="intMajor_ID_dosen" class="form-label">Homebase Prodi</label>
                                <select class="form-select" id="intMajor_ID_dosen" name="intMajor_ID_dosen">
                                    <option value="">Pilih Prodi</option>
                                    @foreach($majors as $major)
                                        <option value="{{ $major->intMajor_ID }}">{{ $major->txtNameMajor }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="txtFieldOfKnowledge" class="form-label">Bidang Keilmuan</label>
                                <input type="text" class="form-control" id="txtFieldOfKnowledge" name="txtFieldOfKnowledge">
                            </div>
                        </div>
                    </div>
                    {{-- ====================================================== --}}
                    <div class="mb-3">
                        <label for="txtPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="txtPassword" name="txtPassword">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btn-save">Simpan</button>
            </div>
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
    var table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        // Gunakan URL saat ini (termasuk parameter) untuk AJAX
        ajax: '{{ route('users.data') }}' + window.location.search,
        columns: [
            { data: 'intUser_ID', name: 'intUser_ID' },
            { data: 'txtFullName', name: 'txtFullName' },
            { data: 'txtEmail', name: 'txtEmail' },
            // { data: 'txtNim', name: 'txtNim' },
            { data: 'role', name: 'role', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Fungsi untuk menampilkan/menyembunyikan form dinamis
    function toggleRoleFields() {
        var selectedRole = $('#role').val();

        // Logika untuk Mahasiswa
        if (selectedRole === 'mahasiswa') {
            $('#mahasiswa-fields').slideDown();
            $('#txtNIM, #intMajor_ID').attr('required', true);
        } else {
            $('#mahasiswa-fields').slideUp();
            $('#txtNIM, #intMajor_ID').attr('required', false);
        }

        // Logika untuk Dosen
        if (selectedRole === 'dosen' || selectedRole === 'kaprodi') {
            $('#dosen-fields').slideDown();
            $('#txtNIP, #txtNIDN, #intMajor_ID_dosen').attr('required', true);
        } else {
            $('#dosen-fields').slideUp();
            $('#txtNIP, #txtNIDN, #intMajor_ID_dosen').attr('required', false);
        }
    }

    // Panggil fungsi saat dropdown role berubah
    $('#role').on('change', function() {
        toggleRoleFields();
    });

    // Fungsi untuk memuat peminatan berdasarkan prodi yang dipilih
    $('#intMajor_ID').on('change', function() {
        var majorId = $(this).val();
        var concentrateSelect = $('#intConcentrate_ID');
        concentrateSelect.empty().append('<option value="">Memuat...</option>');

        if (majorId) {
            // Ganti URL ini dengan route yang sesuai untuk mengambil data peminatan
            $.get('/api/concentrates-by-major/' + majorId, function(data) {
                concentrateSelect.empty().append('<option value="">Pilih Peminatan</option>');
                $.each(data, function(key, value) {
                    concentrateSelect.append('<option value="' + value.intConcentrate_ID + '">' + value.txtNameConcentrate + '</option>');
                });
            });
        } else {
            concentrateSelect.empty().append('<option value="">Pilih Prodi terlebih dahulu</option>');
        }
    });

    // Tombol Tambah: Buka Modal
    $('#btn-add-user').click(function() {
        $('#user-form').trigger("reset").parsley().reset();
        $('#user_id').val('');
        $('#userModalLabel').text('Tambah User Baru');
        $('#txtPassword').attr('required', true);
        $('#user-modal').modal('show');
        toggleRoleFields();
    });

    // Tombol Edit: Ambil data & buka modal
    $('body').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $.get("{{ url('users') }}/" + id + '/edit', function(data) {
            $('#user-form').trigger("reset").parsley().reset();
            $('#userModalLabel').text('Edit User');
            $('#user_id').val(data.intUser_ID);
            $('#txtFullName').val(data.txtFullName);
            $('#txtEmail').val(data.txtEmail);
            // $('#txtNim').val(data.txtNim);
            $('#txtGender').val(data.txtGender);
            $('#txtBirthPlace').val(data.txtBirthPlace);
            $('#txtPhone').val(data.txtPhone);
            // $('#txtYear').val(data.txtYear);
            if (data.dtmBirthDate) {
                var birthDate = new Date(data.dtmBirthDate);
                var formattedDate = birthDate.getFullYear() + '-' +
                                    ('0' + (birthDate.getMonth() + 1)).slice(-2) + '-' +
                                    ('0' + birthDate.getDate()).slice(-2);
                $('#dtmBirthDate').val(formattedDate);
            }
            // Pilih role yang sesuai
            if (data.roles.length > 0) {
                $('#role').val(data.roles[0].name);
            }
            $('#txtPassword').attr('required', false);
            // Cek dan isi data profil mahasiswa jika ada
            if (data.mahasiswa_profile) {
                $('#txtNIM').val(data.mahasiswa_profile.txtNIM);
                $('#txtYear').val(data.mahasiswa_profile.txtYear);
                $('#intMajor_ID').val(data.mahasiswa_profile.intMajor_ID).trigger('change'); // trigger change untuk load peminatan

                // Beri jeda agar peminatan selesai dimuat
                setTimeout(function() {
                    $('#intConcentrate_ID').val(data.mahasiswa_profile.intConcentrate_ID);
                }, 500);
            }
            // Cek dan isi data profil dosen jika ada
            if (data.dosen_profile) {
                $('#txtNIP').val(data.dosen_profile.txtNIP);
                $('#txtNIDN').val(data.dosen_profile.txtNIDN);
                $('#txtFieldOfKnowledge').val(data.dosen_profile.txtFieldOfKnowledge);
                $('#intMajor_ID_dosen').val(data.dosen_profile.intMajor_ID);
            }
            toggleRoleFields();
            $('#user-modal').modal('show');
        });
    });

    // Tombol Simpan (Create & Update)
    $('#btn-save').click(function(e) {
        e.preventDefault();
        if ($('#user-form').parsley().validate()) {
            var id = $('#user_id').val();
            var url = id ? "{{ url('users') }}/" + id : "{{ route('users.store') }}";
            var method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $('#user-form').serialize(),
                success: function(response) {
                    $('#user-modal').modal('hide');
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
                    url: "{{ url('users') }}/" + id,
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
