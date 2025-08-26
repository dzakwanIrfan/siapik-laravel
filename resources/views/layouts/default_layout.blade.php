<!DOCTYPE html>
<html lang="id">
  @include('includes.head')

<body>
    @if (file_exists(public_path('mazer/assets/static/js/initTheme.js')))
      <script src="{{ asset('mazer/assets/static/js/initTheme.js') }}"></script>
    @endif

    <div id="app">
      <div id="sidebar">
        @include('includes.sidebar')
      </div>

      <div id="main" class="{{ ($layoutNavbar ?? false) ? 'layout-navbar navbar-fixed' : '' }}">
        @if ($layoutNavbar ?? false)
          @include('includes.topbar')
        @endif

        @unless ($layoutNavbar ?? false)
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
        @endunless

        <div id="main-content">
            <div class="page-heading">
                @hasSection('page_title')
                    <div class="page-title">
                        @yield('page_title')
                    </div>
                @endif
                <section class="section">
                    @yield('content')
                </section>
            </div>
        </div>

        <footer>
            <div class="footer clearfix mb-0 text-muted">
                <hr>
                <div class="float-start">
                    <p>{{ now()->year }} &copy; SIAPIK</p>
                </div>
            </div>
        </footer>
      </div>
    </div>

    {{-- =================================================================
     BAGIAN PENTING: SEMUA FILE JAVASCRIPT DIMUAT DI SINI
    ================================================================== --}}

    {{-- 1. MUAT SEMUA LIBRARY EKSTERNAL TERLEBIH DAHULU --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @include('includes.page-js') {{-- Script dari template Mazer --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>

    {{-- 2. SETELAH LIBRARY SIAP, JALANKAN KODE JQUERY ANDA --}}
    <script>
    $(document).ready(function() {

        // TAMBAHKAN AJAX SETUP DI SINI
        // Ini akan berlaku untuk semua halaman yang memakai layout ini
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Kode Toast SweetAlert2 Anda (ini sudah benar)
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

        @if(Session::has('success'))
            Toast.fire({ icon: "success", title: "{{ Session::get('success') }}" });
        @endif

        @if(Session::has('error'))
            Toast.fire({ icon: "error", title: "{{ Session::get('error') }}" });
        @endif
    });
    </script>

    {{-- 3. TERAKHIR, JALANKAN SCRIPT SPESIFIK PER HALAMAN (dari @push) --}}
    @stack('scripts')
</body>
</html>
