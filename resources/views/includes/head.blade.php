<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title', config('app.name'))</title>

  {{-- Favicon sederhana --}}
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.ico') }}">

  {{-- Core Mazer CSS (compiled) --}}
  <link rel="stylesheet" href="{{ asset('mazer/assets/compiled/css/app.css') }}">
  @if (file_exists(public_path('mazer/assets/compiled/css/app-dark.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/compiled/css/app-dark.css') }}">
  @endif

  {{-- Icons --}}
  @if (file_exists(public_path('mazer/assets/extensions/bootstrap-icons/font/bootstrap-icons.min.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/bootstrap-icons/font/bootstrap-icons.min.css') }}">
  @endif
  @if (file_exists(public_path('mazer/assets/extensions/@fortawesome/fontawesome-free/css/all.min.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/@fortawesome/fontawesome-free/css/all.min.css') }}">
  @endif
  {{-- Dripicons (opsional) --}}
  @if (file_exists(public_path('mazer/assets/extensions/@icon/dripicons/icons/dripicons.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/@icon/dripicons/icons/dripicons.css') }}">
  @endif

  {{-- Plugins CSS (safe-guard dengan file_exists) --}}
  @if (file_exists(public_path('mazer/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
  @endif
  @if (file_exists(public_path('mazer/assets/extensions/simple-datatables/style.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/simple-datatables/style.css') }}">
  @endif
  @if (file_exists(public_path('mazer/assets/extensions/choices.js/public/assets/styles/choices.min.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/choices.js/public/assets/styles/choices.min.css') }}">
  @endif
  @if (file_exists(public_path('mazer/assets/extensions/flatpickr/flatpickr.min.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/flatpickr/flatpickr.min.css') }}">
  @endif
  @if (file_exists(public_path('mazer/assets/extensions/jsvectormap/css/jsvectormap.min.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/jsvectormap/css/jsvectormap.min.css') }}">
  @endif
  @if (file_exists(public_path('mazer/assets/extensions/filepond/filepond.min.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/filepond/filepond.min.css') }}">
  @endif
  @if (file_exists(public_path('mazer/assets/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
  @endif
  @if (file_exists(public_path('mazer/assets/extensions/summernote/summernote-lite.min.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/summernote/summernote-lite.min.css') }}">
  @endif
  @if (file_exists(public_path('mazer/assets/extensions/quill/quill.snow.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/quill/quill.snow.css') }}">
  @endif
  @if (file_exists(public_path('mazer/assets/extensions/toastify-js/src/toastify.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/toastify-js/src/toastify.css') }}">
  @endif
  @if (file_exists(public_path('mazer/assets/extensions/perfect-scrollbar/perfect-scrollbar.css')))
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/perfect-scrollbar/perfect-scrollbar.css') }}">
  @endif

  @stack('styles')
</head>
