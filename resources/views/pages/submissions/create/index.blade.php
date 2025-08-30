@php($layoutNavbar = true)

@extends('layouts.default_layout')
@section('title', 'Pengajuan Surat')

@section('page_title')
  <!-- BEGIN breadcrumb -->
  <nav aria-label="breadcrumb" class="breadcrumb-header float-end float-lg-end">
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Daftar Surat</li>
      </ol>
  </nav>
  <!-- END breadcrumb -->

  <!-- BEGIN page-header -->
  <div>
    <h3>Daftar Surat</h3>
    <p class="text-subtitle text-muted">Sistem Informasi Administrasi Pelayanan Akademik</p>
  </div>
  <!-- END page-header -->
@endsection

@section('content')
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
    @forelse($letter_types as $type)
      <div class="col d-flex">
        <div class="card border shadow-sm w-100 h-100">
          <div class="card-header bg-white border-bottom">
            <div class="d-flex flex-column">
              <h6 class="mb-1 text-dark lh-sm text-wrap text-break">
                {{ $type->txtNameLetterType }}
              </h6>
            </div>
          </div>

          <div class="card-body d-flex flex-column">
            <p class="text-muted my-3 small">{{ $type->txtDescription }}</p>
            <div class="mt-auto">
              <button type="button"
                      class="btn btn-primary w-100 btn-open-letter"
                      data-bs-toggle="modal"
                      data-bs-target="#letterModal"
                      data-type-id="{{ $type->intLetterType_ID }}"
                      data-type-name="{{ $type->txtNameLetterType }}">
                Ajukan Surat
              </button>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col">
        <div class="alert alert-warning border-start border-4 border-warning">
          <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill text-warning me-3"></i>
            <div>
              <h6 class="mb-1">Tidak Ada Data</h6>
              <p class="mb-0">Belum ada jenis surat yang tersedia saat ini.</p>
            </div>
          </div>
        </div>
      </div>
    @endforelse
  </div>

  <div class="modal fade" id="letterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header border-bottom">
          <div>
            <h5 class="modal-title mb-0">Form Pengajuan Surat</h5>
            <small class="text-muted">Jenis: <span id="modalLetterName" class="fw-medium"></span></small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body p-4">
          <form id="dynamicForm"
                action="{{ route('submissions.store') }}"
                method="POST"
                enctype="multipart/form-data"
                data-parsley-validate>
            <div id="dynamicFields"><!-- akan diisi via AJAX --></div>
          </form>
        </div>
        
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="btnDummySubmit">Kirim Pengajuan</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
(function () {
  const modal     = document.getElementById('letterModal');        // id modal kamu
  const form      = document.getElementById('dynamicForm');        // <form id="dynamicForm" ... data-parsley-validate>
  const wrap      = document.getElementById('dynamicFields');      // container isi form dinamis
  const submitBtn = document.getElementById('btnDummySubmit');     // tombol submit di modal

  // --- Helpers: inisialisasi plugin UI (tanpa custom CSS) ---
  function initSelect2(scope) {
    if (window.jQuery && jQuery.fn.select2) {
      jQuery(scope).find('.default-select2').each(function () {
        if (jQuery(this).data('select2')) return;
        jQuery(this).select2({
          width: '100%',
          dropdownParent: jQuery('#letterModal')
        });
      });
    }
  }
  function initFlatpickr(scope) {
    if (window.flatpickr) {
      scope.querySelectorAll('.flatpickr-input').forEach(function (el) {
        if (!el._fp) {
          flatpickr(el, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true
          });
        }
      });
    }
  }
  function initFilePond(scope) {
    if (window.FilePond) {
      try {
        if (window.FilePondPluginImagePreview) FilePond.registerPlugin(FilePondPluginImagePreview);
        if (window.FilePondPluginFileValidateType) FilePond.registerPlugin(FilePondPluginFileValidateType);
      } catch(e){}
      scope.querySelectorAll('input[type="file"].filepond').forEach(function (el) {
        if (!el._pond) {
          const pond = FilePond.create(el, {
            allowMultiple: false,
            credits: false,
            storeAsFile: true
          });
          el._pond = pond;
        }
      });
    }
  }

  // --- Parsley: destroy + re-init tiap kali form dinamis dimuat ---
  function initParsley() {
    if (!(window.jQuery && jQuery.fn.parsley && form)) return;
    const $form = jQuery(form);
    try { $form.parsley().destroy(); } catch (e) {}

    $form.parsley({
      trigger: 'change',
      errorClass: 'is-invalid',
      successClass: 'is-valid',
      errorsWrapper: '<div class="invalid-feedback"></div>',
      errorTemplate: '<span></span>',
      // taruh class invalid ke elemen yang kelihatan (select2/filepond)
      classHandler: function (field) {
        const $el = field.$element;
        // Select2
        if ($el.hasClass('select2-hidden-accessible')) {
          return $el.next('.select2').find('.select2-selection');
        }
        // FilePond
        if ($el.hasClass('filepond') && $el.get(0)?._pond) {
          return jQuery($el.get(0)._pond.element);
        }
        // Default: input/textarea/select biasa
        return $el;
      },
      // letak pesan error
      errorsContainer: function (field) {
        const $el = field.$element;
        return $el.closest('.form-group');
      }
    });
  }

  function initEnhancersAndParsley() {
    initSelect2(wrap);
    initFlatpickr(wrap);
    initFilePond(wrap);
    initParsley();
  }

  // --- Load partial form saat klik kartu ---
  document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.btn-open-letter');
    if (!btn) return;

    const typeId   = btn.getAttribute('data-type-id');
    const typeName = btn.getAttribute('data-type-name') || '';
    const titleEl  = document.getElementById('modalLetterName');
    if (titleEl) titleEl.textContent = typeName;

    wrap.innerHTML = '<div class="text-center py-5"><div class="spinner-border" role="status"></div><div class="mt-2">Memuat formulir...</div></div>';

    try {
      const res  = await fetch(`{{ url('/submissions/types') }}/${typeId}/form`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const html = await res.text();
      wrap.innerHTML = html;
      initEnhancersAndParsley();
    } catch (err) {
      wrap.innerHTML = '<div class="alert alert-danger">Gagal memuat formulir.</div>';
    }
  });

  // Reset state validasi saat modal dibuka
  if (modal && window.bootstrap) {
    modal.addEventListener('shown.bs.modal', function () {
      if (window.jQuery && jQuery.fn.parsley) {
        jQuery(form).parsley().reset();
      }
    });
  }

  // --- Submit: validasi Parsley -> spinner -> submit; error -> toast ---
  if (submitBtn && form) {
    submitBtn.addEventListener('click', function () {
      const btn   = this;
      const Toast = (typeof Swal !== 'undefined')
        ? Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (t) => { t.onmouseenter = Swal.stopTimer; t.onmouseleave = Swal.resumeTimer; }
          })
        : null;

      let valid = true;
      if (window.jQuery && jQuery.fn.parsley) {
        valid = jQuery(form).parsley().validate();
      } else {
        valid = form.checkValidity();
        if (!valid) form.reportValidity();
      }

      if (valid) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        form.submit();
      } else {
        Toast && Toast.fire({ icon: "error", title: "Data tidak valid/lengkap" });
      }
    });
  }
})();
</script>
@endpush


