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
            <div class="d-flex align-items-center justify-content-between">
              <h6 class="mb-0 text-dark text-truncate">{{ $type->txtNameLetterType }}</h6>
              <span class="badge bg-secondary flex-shrink-0">{{ $type->txtCode }}</span>
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
          <form id="dynamicForm" data-parsley-validate>
            <div id="dynamicFields"></div>
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
  window.FIELDS_BY_TYPE = @json($preload ?? []);

  function esc(s){
    return String(s ?? '').replace(/[&<>"']/g, function(c){
      return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]);
    });
  }

  function optionTag(val, label){
    const v = String(val ?? '');
    const l = String(label ?? val ?? '');
    return `<option value="${esc(v)}">${esc(l)}</option>`;
  }

  function parsleyAttrs(validation){
    if(!validation || typeof validation !== 'object') return '';
    return Object.entries(validation).map(([k,v]) => `data-parsley-${k}="${esc(v)}"`).join(' ');
  }

  function buildFieldGroup(f){
    const id = `fld_${f.name}`;
    const required = f.required ? 'required' : '';
    const pattrs = parsleyAttrs(f.validation);
    const help = f.help ? `<div class="form-text text-muted small">${esc(f.help)}</div>` : '';
    let control = '';

    switch (f.type) {
      case 'textarea':
        control = `<textarea class="form-control" id="${id}" name="fields[${esc(f.name)}]" ${required} ${pattrs} rows="3" placeholder="${esc(f.label)}"></textarea>`;
        break;

      case 'select':
        {
          const opts = Array.isArray(f.options) ? f.options : [];
          const optHtml = ['<option value="">-- Pilih --</option>'].concat(
            opts.map(o => {
              if (o && typeof o === 'object') {
                return optionTag(o.value ?? o.id ?? o.key, o.label ?? o.text ?? o.name ?? o.value ?? o.id);
              } else {
                return optionTag(o, o);
              }
            })
          ).join('');
          control = `<select class="form-select default-select2" id="${id}" name="fields[${esc(f.name)}]" ${required} ${pattrs}>${optHtml}</select>`;
        }
        break;

      case 'date':
        control = `<input type="text" class="form-control flatpickr-input" id="${id}" name="fields[${esc(f.name)}]" ${required} ${pattrs} placeholder="Pilih tanggal" autocomplete="off" readonly>`;
        break;

      case 'file':
        {
          const acceptAttr = (f.validation && f.validation.accept) ? `accept="${esc(f.validation.accept)}"` : '';
          control = `<input type="file" class="filepond" id="${id}" name="fields[${esc(f.name)}]" ${required} ${pattrs} ${acceptAttr} data-max-files="1">`;
        }
        break;

      case 'number':
        control = `<input type="number" class="form-control" id="${id}" name="fields[${esc(f.name)}]" ${required} ${pattrs} placeholder="${esc(f.label)}">`;
        break;

      case 'email':
        control = `<input type="email" class="form-control" id="${id}" name="fields[${esc(f.name)}]" ${required} ${pattrs} placeholder="${esc(f.label)}">`;
        break;

      default:
        control = `<input type="text" class="form-control" id="${id}" name="fields[${esc(f.name)}]" ${required} ${pattrs} placeholder="${esc(f.label)}">`;
    }

    return `
      <div class="mb-3">
        <label for="${id}" class="form-label fw-medium">
          ${esc(f.label)} ${f.required ? '<span class="text-danger">*</span>' : ''}
        </label>
        ${control}
        ${help}
      </div>`;
  }

  function hydrateModal(typeId, typeName){
    document.getElementById('modalLetterName').textContent = typeName;
    const fields = (window.FIELDS_BY_TYPE && window.FIELDS_BY_TYPE[typeId]) ? window.FIELDS_BY_TYPE[typeId] : [];
    
    if (fields.length === 0) {
      document.getElementById('dynamicFields').innerHTML = `
        <div class="text-center py-4">
          <i class="bi bi-exclamation-circle text-warning fs-1"></i>
          <h6 class="mt-3 text-warning">Belum Ada Form</h6>
          <p class="text-muted">Form untuk jenis surat ini belum dikonfigurasi.</p>
        </div>`;
      return;
    }
    
    const html = fields.map(buildFieldGroup).join('');
    document.getElementById('dynamicFields').innerHTML = html;

    document.querySelectorAll('#dynamicFields .default-select2').forEach(function(el){
      new Choices(el, { searchEnabled: true, removeItemButton: false, shouldSort: false, placeholder: true });
    });

    if (window.flatpickr) {
      flatpickr('.flatpickr-input', { 
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true
      });
    }

    if (window.FilePond) {
      try {
        if (window.FilePondPluginImagePreview) FilePond.registerPlugin(FilePondPluginImagePreview);
        if (window.FilePondPluginFileValidateType) FilePond.registerPlugin(FilePondPluginFileValidateType);
      } catch (e) {}
      document.querySelectorAll('#dynamicFields input[type="file"].filepond').forEach(function(el){
        FilePond.create(el, {
          allowMultiple: false,
          credits: false,
          storeAsFile: true,
          allowImagePreview: true,
          stylePanelLayout: 'compact',
          labelIdle: 'Seret & lepas atau <span class="filepond--label-action">pilih file</span>'
        });
      });
    }

    if (window.jQuery && jQuery.fn.parsley) {
      jQuery('#dynamicForm').parsley({
        errorClass: 'is-invalid',
        successClass: 'is-valid',
        errorsWrapper: '<div class="invalid-feedback"></div>',
        errorTemplate: '<span></span>'
      });
    }
  }

  document.addEventListener('click', function(e){
    const btn = e.target.closest('.btn-open-letter');
    if (!btn) return;
    hydrateModal(btn.getAttribute('data-type-id'), btn.getAttribute('data-type-name'));
  });

  document.getElementById('btnDummySubmit').addEventListener('click', function(){
    const btn = this;
    if (window.jQuery && jQuery.fn.parsley) {
      const parsley = jQuery('#dynamicForm').parsley();
      const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });
      if (parsley.validate()) {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        btn.disabled = true;
        setTimeout(() => {
          btn.innerHTML = 'Kirim Pengajuan';
          btn.disabled = false;
          Toast.fire({
            icon: "success",
            title: "Pengajuan surat berhasil dikirim"
          });
        }, 1000);
      } else {
        Toast.fire({
          icon: "error",
          title: "Data tidak lengkap"
        });
      }
    }
  });
</script>
@endpush
