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

      {{-- Tambah class khusus saat navbar atas dipakai --}}
      <div id="main" class="{{ ($layoutNavbar ?? false) ? 'layout-navbar navbar-fixed' : '' }}">
        {{-- TOP NAVBAR (opsional) --}}
        @if ($layoutNavbar ?? false)
          @include('includes.topbar')
        @endif

        {{-- Header kecil untuk burger di mobile (saat tanpa navbar atas) --}}
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

    @include('includes.page-js')
    @stack('scripts')
</body>
</html>
