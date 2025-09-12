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
    <h3>Profile</h3>
    <p class="text-subtitle text-muted">Perbarui informasi data diri Anda di sini.</p>
  </div>
  <!-- END page-header -->
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Profil</h4>
            </div>
            <div class="card-body">
                <form id="profile-form" action="{{ route('profile.update') }}" method="POST" data-parsley-validate>
                    @csrf
                    @method('PUT')

                    <h5 class="mt-2">Informasi Dasar</h5>
                    <hr>
                                        <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="txtFullName" class="form-label">Nama Lengkap</label>
                                <input type="text" name="txtFullName" id="txtFullName" class="form-control" value="{{ $user->txtFullName }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="txtEmail" class="form-label">Email</label>
                                <input type="email" id="txtEmail" class="form-control" value="{{ $user->txtEmail }}" readonly>
                                <small class="form-text text-muted">Email tidak dapat diubah.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="txtBirthPlace" class="form-label">Tempat Lahir</label>
                                <input type="text" name="txtBirthPlace" id="txtBirthPlace" class="form-control" value="{{ $user->txtBirthPlace }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group mb-3">
                                <label for="dtmBirthDate" class="form-label">Tanggal Lahir</label>
                                <input type="date" name="dtmBirthDate" id="dtmBirthDate" class="form-control" value="{{ $user->dtmBirthDate ? \Carbon\Carbon::parse($user->dtmBirthDate)->format('Y-m-d') : '' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="txtGender" class="form-label">Jenis Kelamin</label>
                                <select name="txtGender" id="txtGender" class="form-select">
                                    <option value="L" {{ $user->txtGender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ $user->txtGender == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    @if($user->hasRole('mahasiswa') && $user->mahasiswaProfile)
                        <h5 class="mt-4">Profil Mahasiswa</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="txtNIM" class="form-label">NIM</label>
                                <input type="text" name="txtNIM" id="txtNIM" class="form-control" value="{{ $user->mahasiswaProfile->txtNIM }}" required>
                            </div>
                             <div class="col-md-6 form-group mb-3">
                                <label for="txtYear" class="form-label">Tahun Angkatan</label>
                                <input type="text" name="txtYear" id="txtYear" class="form-control" value="{{ $user->mahasiswaProfile->txtYear }}">
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="intMajor_ID" class="form-label">Program Studi</label>
                                {{-- Dropdown untuk Prodi --}}
                                <select class="form-select" id="intMajor_ID" name="intMajor_ID" required>
                                    <option value="">Pilih Prodi</option>
                                    @foreach($majors as $major)
                                        <option value="{{ $major->intMajor_ID }}" {{ $user->mahasiswaProfile->intMajor_ID == $major->intMajor_ID ? 'selected' : '' }}>
                                            {{ $major->txtNameMajor }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="intConcentrate_ID" class="form-label">Peminatan</label>
                                {{-- Dropdown untuk Peminatan (akan diisi oleh AJAX) --}}
                                <select class="form-select" id="intConcentrate_ID" name="intConcentrate_ID">
                                    <option value="">Pilih Prodi terlebih dahulu</option>
                                </select>
                            </div>
                        </div>
                    @endif

                    @if($user->hasRole(['dosen', 'kaprodi']) && $user->dosenProfile)
                        <h5 class="mt-4">Profil Dosen</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="txtNIP" class="form-label">NIP</label>
                                    <input type="text" name="txtNIP" id="txtNIP" class="form-control" value="{{ $user->dosenProfile->txtNIP }}">
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="txtNIDN" class="form-label">NIDN</label>
                                    <input type="text" name="txtNIDN" id="txtNIDN" class="form-control" value="{{ $user->dosenProfile->txtNIDN }}">
                                </div>
                            </div>
                        </div>
                         <div class="row">
                             <div class="col-md-6">
                                <label for="intMajor_ID_dosen" class="form-label">Homebase Prodi</label>
                                <select class="form-select mb-3" id="intMajor_ID_dosen" name="intMajor_ID_dosen">
                                    <option value="">Homebase Prodi</option>
                                    @foreach($majors as $major)
                                        <option value="{{ $major->intMajor_ID }}" {{ $user->dosenProfile->intMajor_ID == $major->intMajor_ID ? 'selected' : '' }}>
                                            {{ $major->txtNameMajor }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="txtFieldOfKnowledge" class="form-label">Bidang Keilmuan</label>
                                    <input type="text" name="txtFieldOfKnowledge" id="txtFieldOfKnowledge" class="form-control" value="{{ $user->dosenProfile->txtFieldOfKnowledge }}">
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group mt-4">
                        <button type="submit" id="btn-save-profile" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // Fungsi untuk memuat peminatan
    function loadConcentrates(majorId, selectedConcentrateId = null) {
        var concentrateSelect = $('#intConcentrate_ID');
        if (!majorId) {
            concentrateSelect.html('<option value="">Pilih Prodi terlebih dahulu</option>');
            return;
        }

        concentrateSelect.html('<option value="">Memuat...</option>');
        $.get('/api/concentrates-by-major/' + majorId, function(data) {
            concentrateSelect.empty().append('<option value="">Pilih Peminatan (Opsional)</option>');
            $.each(data, function(key, value) {
                concentrateSelect.append(`<option value="${value.intConcentrate_ID}">${value.txtNameConcentrate}</option>`);
            });
            // Jika ada ID peminatan yang sudah dipilih sebelumnya, pilih opsi tersebut
            if (selectedConcentrateId) {
                concentrateSelect.val(selectedConcentrateId);
            }
        });
    }

    // Panggil saat dropdown prodi berubah
    $('#intMajor_ID').on('change', function() {
        loadConcentrates($(this).val());
    });

    // Panggil saat halaman pertama kali dimuat untuk mengisi peminatan yang sudah ada
    @if($user->hasRole('mahasiswa') && $user->mahasiswaProfile)
        loadConcentrates('{{ $user->mahasiswaProfile->intMajor_ID }}', '{{ $user->mahasiswaProfile->intConcentrate_ID }}');
    @endif

    $('#profile-form').on('submit', function(e) {
        e.preventDefault();

        if (!$(this).parsley().isValid()) {
            return;
        }

        var form = $(this);
        var submitButton = $('#btn-save-profile');
        var originalButtonText = submitButton.html();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',

            data: new FormData(this),
            processData: false,
            contentType: false,

            beforeSend: function() {
                submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: response.success,
                });
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorString = '<ul>';
                $.each(errors, function(key, value) {
                    errorString += `<li>${value[0]}</li>`;
                });
                errorString += '</ul>';
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: errorString,
                });
            },
            complete: function() {
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});
</script>
@endpush
