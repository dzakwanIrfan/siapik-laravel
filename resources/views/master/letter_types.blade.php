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
    <h3>Manajemen Jenis Surat</h3>
    <table class="table table-bordered" id="letter-types-table" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Jenis Surat</th>
                <th>Kode</th>
                <th>Deskripsi</th>
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
                <h5 class="modal-title" id="formModalLabel">Form Jenis Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="letter-type-form" data-parsley-validate>
                <div class="modal-body">
                    <input type="hidden" id="letter_type_id" name="letter_type_id">
                    <div class="mb-3">
                        <label for="txtNameLetterType" class="form-label">Nama Jenis Surat</label>
                        <input type="text" class="form-control" id="txtNameLetterType" name="txtNameLetterType" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="txtCode" class="form-label">Kode Surat</label>
                            <input type="text" class="form-control" id="txtCode" name="txtCode" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="txtTemplatePath" class="form-label">Path Template</label>
                            <input type="text" class="form-control" id="txtTemplatePath" name="txtTemplatePath" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="txtDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="txtDescription" name="txtDescription" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="bitActive" class="form-label">Status</label>
                        <select class="form-select" id="bitActive" name="bitActive" required>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
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

<!-- Modal Template Editor Full Height -->
<div class="modal fade" id="template-editor-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content d-flex flex-column">
            <div class="modal-header">
                <h5 class="modal-title" id="templateEditorModalLabel">Edit Template Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body flex-fill p-3">
                <div class="h-100 d-flex flex-column">
                    <div class="mb-2">
                        <label class="form-label fw-bold mb-1">Template Editor</label>
                    </div>
                    <div class="flex-fill">
                        <div id="template-editor" class="h-100 border"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btn-preview">Update Preview</button>
                <button type="button" class="btn btn-primary" id="btn-save-template">Simpan Template</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form untuk Preview di tab baru -->
<form id="preview-form" method="POST" target="_blank" style="display: none;">
    @csrf
    <input type="hidden" name="content" id="preview-content">
</form>
@endsection

@push('scripts')
<!-- CodeMirror CSS -->
<link rel="stylesheet" href="{{ asset('codemirror/codemirror.min.css') }}">
<link rel="stylesheet" href="{{ asset('codemirror/monokai.min.css') }}">

<!-- CodeMirror JS -->
<script src="{{ asset('codemirror/codemirror.min.js') }}"></script>
<script src="{{ asset('codemirror/xml.min.js') }}"></script>
<script src="{{ asset('codemirror/htmlmixed.min.js') }}"></script>
<script src="{{ asset('codemirror/css.min.js') }}"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    let editor;
    let currentLetterTypeId;

    var table = $('#letter-types-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('letter-types.data') }}',
        columns: [
            { data: 'intLetterType_ID', name: 'intLetterType_ID' },
            { data: 'txtNameLetterType', name: 'txtNameLetterType' },
            { data: 'txtCode', name: 'txtCode' },
            { data: 'txtDescription', name: 'txtDescription' },
            { data: 'bitActive', name: 'bitActive' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Initialize CodeMirror dengan pengaturan tinggi dinamis
    function initializeEditor() {
        if (!editor) {
            editor = CodeMirror(document.getElementById('template-editor'), {
                mode: 'htmlmixed',
                theme: 'monokai',
                lineNumbers: true,
                autoCloseTags: true,
                matchBrackets: true,
                indentUnit: 2,
                tabSize: 2,
                lineWrapping: true,
                foldGutter: true,
                gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                extraKeys: {
                    "Ctrl-Space": "autocomplete",
                    "F11": function(cm) {
                        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                    },
                    "Esc": function(cm) {
                        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                    }
                }
            });
            
            // Set tinggi editor secara manual setelah inisialisasi
            resizeEditor();
        }
    }

    // Function untuk resize editor agar full height
    function resizeEditor() {
        if (editor) {
            // Hitung tinggi yang tersedia
            const modalBody = document.querySelector('#template-editor-modal .modal-body');
            const modalHeader = document.querySelector('#template-editor-modal .modal-header');
            const modalFooter = document.querySelector('#template-editor-modal .modal-footer');
            const label = document.querySelector('#template-editor-modal .form-label');
            
            const windowHeight = window.innerHeight;
            const headerHeight = modalHeader ? modalHeader.offsetHeight : 60;
            const footerHeight = modalFooter ? modalFooter.offsetHeight : 70;
            const labelHeight = label ? label.offsetHeight : 25;
            const padding = 50; // padding dan margin tambahan
            
            const availableHeight = windowHeight - headerHeight - footerHeight - labelHeight - padding;
            
            // Set tinggi editor
            const editorElement = document.querySelector('.CodeMirror');
            if (editorElement) {
                editorElement.style.height = availableHeight + 'px';
                editor.refresh();
            }
        }
    }

    // Function untuk update preview - buka di tab baru
    function updatePreview() {
        if (editor && currentLetterTypeId) {
            const content = editor.getValue();
            const form = document.getElementById('preview-form');
            const contentInput = document.getElementById('preview-content');
            
            // Set form action ke route preview
            form.action = "{{ url('letter-types') }}/" + currentLetterTypeId + "/preview-template";
            contentInput.value = content;
            
            // Submit form ke tab baru
            form.submit();
            
            // Tampilkan notifikasi
            Swal.fire({
                title: 'Preview Dibuka',
                text: 'Preview template telah dibuka di tab baru',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }

    $('#btn-add').click(function() {
        $('#letter-type-form').trigger("reset").parsley().reset();
        $('#letter_type_id').val('');
        $('#formModalLabel').text('Tambah Jenis Surat Baru');
        $('#form-modal').modal('show');
    });

    $('body').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $.get("{{ url('letter-types') }}/" + id + '/edit', function(data) {
            $('#letter-type-form').trigger("reset").parsley().reset();
            $('#formModalLabel').text('Edit Jenis Surat');
            $('#letter_type_id').val(data.intLetterType_ID);
            $('#txtNameLetterType').val(data.txtNameLetterType);
            $('#txtCode').val(data.txtCode);
            $('#txtDescription').val(data.txtDescription);
            $('#txtTemplatePath').val(data.txtTemplatePath);
            $('#bitActive').val(data.bitActive);
            $('#form-modal').modal('show');
        });
    });

    // Edit Template Handler
    $('body').on('click', '.edit-template-btn', function() {
        var id = $(this).data('id');
        currentLetterTypeId = id;
        
        initializeEditor();
        
        $.get("{{ url('letter-types') }}/" + id + '/edit-template', function(response) {
            if (response.success) {
                $('#templateEditorModalLabel').text('Edit Template: ' + response.letterType.txtNameLetterType);
                editor.setValue(response.content);
                
                $('#template-editor-modal').modal('show');
            } else {
                Swal.fire('Error!', response.error, 'error');
            }
        }).fail(function(xhr) {
            Swal.fire('Error!', xhr.responseJSON?.error || 'Gagal memuat template', 'error');
        });
    });

    $('#btn-save').click(function(e) {
        e.preventDefault();
        if ($('#letter-type-form').parsley().validate()) {
            var id = $('#letter_type_id').val();
            var url = id ? "{{ url('letter-types') }}/" + id : "{{ route('letter-types.store') }}";
            var method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $('#letter-type-form').serialize(),
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

    // Update Preview - Buka di tab baru langsung
    $('#btn-preview').click(function() {
        updatePreview();
    });

    // Save Template
    $('#btn-save-template').click(function() {
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
        if (!currentLetterTypeId || !editor) {
            Toast.fire('Error!', 'Template editor tidak tersedia', 'error');
            return;
        }

        const content = editor.getValue();
        
        if (!content.trim()) {
            Toast.fire('Error!', 'Konten template tidak boleh kosong', 'error');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menyimpan perubahan template?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('letter-types') }}/" + currentLetterTypeId + '/update-template',
                    type: 'PUT',
                    data: {
                        content: content,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#template-editor-modal').modal('hide');
                        Toast.fire('Sukses!', response.success, 'success');
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.error || 'Gagal menyimpan template';
                        Toast.fire('Error!', errorMsg, 'error');
                    }
                });
            }
        });
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
                    url: "{{ url('letter-types') }}/" + id,
                    success: function(response) {
                        table.ajax.reload();
                        Swal.fire('Dihapus!', response.success, 'success');
                    }
                });
            }
        });
    });

    // Event handlers untuk resize editor
    $('#template-editor-modal').on('shown.bs.modal', function () {
        setTimeout(function() {
            resizeEditor();
        }, 300);
    });

    $(window).on('resize', function() {
        if ($('#template-editor-modal').hasClass('show')) {
            resizeEditor();
        }
    });
});
</script>
@endpush