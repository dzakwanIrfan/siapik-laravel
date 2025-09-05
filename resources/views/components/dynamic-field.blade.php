@props(['field', 'options' => [], 'value' => null, 'currentValues' => [], 'isRevision' => false])

@php
  $name   = $field->txtFieldName;
  $label  = $field->txtFieldLabel;
  $type   = $field->txtFieldType;
  $req    = (int)$field->bitRequired === 1;
  
  // Gunakan variabel berbeda untuk menghindari konflik
  $fieldValue = old("fields.$name", $value ?? $currentValues[$name] ?? null);
  
  $extra  = $field->jsonFieldValidation ?? [];
  if (is_string($extra)) $extra = @json_decode($extra, true) ?: [];

  // Validasi IPK (0.00–4.00, 2 desimal)
	$isIPK = ($type === 'number' && $name === 'intIPK');
	if ($isIPK) {
		// Tambahkan/merge aturan Parsley (frontend)
		$extra = array_merge([
			'pattern' => '^(?:[0-3]\\.[0-9]{2}|4\\.00)$', // 0.00–3.99 atau 4.00
			'pattern-message' => 'IPK harus antara 0.00 dan 4.00 dengan dua angka di belakang koma, contoh: 3.98',
		], $extra);
	}

  // Parsley attributes dari jsonFieldValidation
  $parsley = [];
  foreach ($extra as $k => $v) {
    $attr = match($k) {
      'minlength' => 'data-parsley-minlength',
      'maxlength' => 'data-parsley-maxlength',
      'min'       => 'data-parsley-min',
      'max'       => 'data-parsley-max',
      'pattern'   => 'data-parsley-pattern',
      'mimes'     => 'data-parsley-mime', // front-end hint; backend tetap pakai 'mimes'
      default     => "data-parsley-{$k}",
    };
    $parsley[$attr] = is_bool($v) ? ($v ? 'true' : 'false') : $v;
  }

  $attrs = collect($parsley)->map(fn($v,$k) => $k.'="'.$v.'"')->implode(' ');
@endphp

<div class="form-group mb-3">
  <label for="{{ $name }}" class="form-label">
    {{ $label }} @if($req)<span class="text-danger">*</span>@endif
  </label>

  @switch($type)
    @case('text')
    @case('email')
    @case('number')
      <input
        type="text"
        class="form-control bg-white shadow-sm @error("fields.$name") is-invalid @enderror"
        id="{{ $name }}"
        name="fields[{{ $name }}]"
        value="{{ $fieldValue }}"
        {{ $req ? 'required' : '' }}
        {!! $attrs ? ''.$attrs : '' !!}
        {{-- Khusus IPK --}}
        @if($isIPK) min="0" max="4" step="0.01" inputmode="decimal" @endif
        placeholder="{{ $isIPK ? 'cth: 3.98' : '' }}"
      >
      @break

    @case('date')
      <input
        type="text"
        class="form-control flatpickr-input bg-white shadow-sm @error("fields.$name") is-invalid @enderror"
        id="{{ $name }}" name="fields[{{ $name }}]"
        value="{{ $fieldValue }}"
        placeholder="Pilih tanggal" autocomplete="off" readonly
        {{ $req ? 'required' : '' }} {!! $attrs ? ''.$attrs : '' !!}>
      @break

    @case('textarea')
      <textarea
        class="form-control bg-white shadow-sm @error("fields.$name") is-invalid @enderror"
        id="{{ $name }}" name="fields[{{ $name }}]" rows="4"
        {{ $req ? 'required' : '' }} {!! $attrs ? ''.$attrs : '' !!}>{{ $fieldValue }}</textarea>
      @break

    @case('select')
      <select
        class="form-select default-select2 @error("fields.$name") is-invalid @enderror"
        id="{{ $name }}" name="fields[{{ $name }}]"
        {{ $req ? 'required' : '' }} {!! $attrs ? ''.$attrs : '' !!}>
        <option value="">-- Pilih {{ $label }} --</option>
        @foreach(($options[$name] ?? []) as $optVal => $optLabel)
          @php
            // support array numerik: [ "A", "B" ] → (value=label)
            $val = is_int($optVal) ? $optLabel : $optVal;
            $lab = is_int($optVal) ? $optLabel : $optLabel;
          @endphp
          <option value="{{ $val }}" @selected($fieldValue == $val)>{{ $lab }}</option>
        @endforeach
      </select>
      @break

    @case('file')
      @php
        $accept = '';
        if (isset($extra['mimes'])) {
          $accept = 'accept=".' . str_replace(',', ',.', $extra['mimes']) . '"';
        } elseif (isset($extra['accept'])) {
          $accept = 'accept="'.$extra['accept'].'"';
        }
        
        // Ambil data file yang sudah ada untuk mode revisi
        $existingFile = null;
        $existingFilePath = null;
        if ($isRevision && isset($currentValues[$name]) && !empty($currentValues[$name])) {
          // Cari submission value untuk mendapatkan metadata
          $submissionValue = null;
          if (isset($GLOBALS['currentSubmission'])) {
            $submissionValue = $GLOBALS['currentSubmission']->values()
              ->where('txtFieldName', $name)
              ->first();
          }
          
          if ($submissionValue && $submissionValue->jsonFieldMeta) {
            $existingFile = $submissionValue->jsonFieldMeta;
            $existingFile['path'] = $submissionValue->txtFieldValue;
            $existingFilePath = $submissionValue->txtFieldValue;
          } else {
            $existingFilePath = $currentValues[$name];
            $existingFile = [
              'path' => $existingFilePath,
              'original_name' => basename($existingFilePath),
              'url' => asset('storage/' . $existingFilePath)
            ];
          }
        }
      @endphp
      
      @if($isRevision)
        {{-- File input untuk revisi (tidak menggunakan FilePond) --}}
        <input
          type="file"
          class="form-control bg-white shadow-sm @error("fields.$name") is-invalid @enderror"
          id="{{ $name }}" 
          name="fields[{{ $name }}]"
          {!! $accept !!}
        >
        
        {{-- Info file yang sudah ada --}}
        @if($existingFile)
          <div class="mt-3 p-3 bg-light rounded border">
            <div class="row align-items-center">
              <div class="col-md-8">
                <h6 class="mb-1 text-dark">File Saat Ini:</h6>
                <p class="mb-1 text-muted">
                  <i class="fas fa-file me-1"></i>
                  {{ $existingFile['original_name'] ?? basename($existingFilePath) }}
                </p>
                @if(isset($existingFile['size']))
                  <small class="text-muted">
                    Ukuran: {{ number_format($existingFile['size'] / 1024, 2) }} KB
                  </small>
                @endif
              </div>
              <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                  <a href="{{ $existingFile['url'] ?? asset('storage/' . $existingFilePath) }}" 
                     class="btn btn-sm btn-outline-primary" 
                     target="_blank"
                     title="Preview File">
                    <i class="fas fa-eye"></i> Preview
                  </a>
                  <a href="{{ $existingFile['url'] ?? asset('storage/' . $existingFilePath) }}" 
                     class="btn btn-sm btn-outline-success" 
                     download="{{ $existingFile['original_name'] ?? basename($existingFilePath) }}"
                     title="Download File">
                    <i class="fas fa-download"></i> Download
                  </a>
                </div>
              </div>
            </div>
          </div>
          <small class="text-muted d-block mt-2">
            <i class="fas fa-info-circle"></i> 
            Biarkan kosong jika tidak ingin mengganti file. Upload file baru jika ingin mengganti.
          </small>
        @endif
        
        @if(!$existingFile && $req)
          <small class="text-danger d-block mt-1">
            <i class="fas fa-exclamation-circle"></i> 
            File wajib diupload karena sebelumnya tidak ada file.
          </small>
        @endif
      @else
        {{-- File input untuk create (menggunakan FilePond) --}}
        <input
          type="file"
          class="filepond @error("fields.$name") is-invalid @enderror"
          id="{{ $name }}" 
          name="fields[{{ $name }}]"
          {{ $req ? 'required' : '' }} 
          {!! $attrs ? ''.$attrs : '' !!} 
          {!! $accept !!} 
          data-max-files="1"
        >
      @endif
      @break
  @endswitch

  @error("fields.$name")
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>