@props(['field', 'options' => [], 'value' => null])

@php
  $name   = $field->txtFieldName;
  $label  = $field->txtFieldLabel;
  $type   = $field->txtFieldType;
  $req    = (int)$field->bitRequired === 1;
  $extra  = $field->jsonFieldValidation ?? [];
  if (is_string($extra)) $extra = @json_decode($extra, true) ?: [];

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
        type="{{ $type === 'text' ? 'text' : ($type === 'email' ? 'email' : 'number') }}"
        class="form-control bg-white shadow-sm @error("fields.$name") is-invalid @enderror"
        id="{{ $name }}" name="fields[{{ $name }}]"
        value="{{ old("fields.$name", $value) }}"
        {{ $req ? 'required' : '' }} {!! $attrs ? ''.$attrs : '' !!}>
      @break

    @case('date')
      <input
        type="text"
        class="form-control flatpickr-input bg-white shadow-sm @error("fields.$name") is-invalid @enderror"
        id="{{ $name }}" name="fields[{{ $name }}]"
        value="{{ old("fields.$name", $value) }}"
        placeholder="Pilih tanggal" autocomplete="off" readonly
        {{ $req ? 'required' : '' }} {!! $attrs ? ''.$attrs : '' !!}>
      @break

    @case('textarea')
      <textarea
        class="form-control bg-white shadow-sm @error("fields.$name") is-invalid @enderror"
        id="{{ $name }}" name="fields[{{ $name }}]" rows="4"
        {{ $req ? 'required' : '' }} {!! $attrs ? ''.$attrs : '' !!}>{{ old("fields.$name", $value) }}</textarea>
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
          <option value="{{ $val }}" @selected(old("fields.$name", $value)==$val)>{{ $lab }}</option>
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
      @endphp
      <input
        type="file"
        class="filepond @error("fields.$name") is-invalid @enderror"
        id="{{ $name }}" name="fields[{{ $name }}]"
        {{ $req ? 'required' : '' }} {!! $attrs ? ''.$attrs : '' !!} {!! $accept !!} data-max-files="1">
      @break
  @endswitch

  @error("fields.$name")
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
