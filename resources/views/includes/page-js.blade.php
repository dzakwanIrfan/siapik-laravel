{{-- Core JS --}}
@if (file_exists(public_path('mazer/assets/static/js/components/dark.js')))
  <script src="{{ asset('mazer/assets/static/js/components/dark.js') }}"></script>
@endif
@if (file_exists(public_path('mazer/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js')))
  <script src="{{ asset('mazer/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
@endif
<script src="{{ asset('mazer/assets/compiled/js/app.js') }}"></script>

{{-- jQuery (untuk plugin yang membutuhkannya) --}}
@if (file_exists(public_path('mazer/assets/extensions/jquery/jquery.min.js')))
  <script src="{{ asset('mazer/assets/extensions/jquery/jquery.min.js') }}"></script>
@endif

{{-- Day.js + locale Indonesia --}}
@if (file_exists(public_path('mazer/assets/extensions/dayjs/dayjs.min.js')))
  <script src="{{ asset('mazer/assets/extensions/dayjs/dayjs.min.js') }}"></script>
  @if (file_exists(public_path('mazer/assets/extensions/dayjs/locale/id.js')))
    <script src="{{ asset('mazer/assets/extensions/dayjs/locale/id.js') }}"></script>
    <script>try{dayjs.locale('id')}catch(e){}</script>
  @endif
@endif

{{-- Chart.js --}}
@if (file_exists(public_path('mazer/assets/extensions/chart.js/chart.umd.js')))
  <script src="{{ asset('mazer/assets/extensions/chart.js/chart.umd.js') }}"></script>
@endif

{{-- ApexCharts --}}
@if (file_exists(public_path('mazer/assets/extensions/apexcharts/apexcharts.min.js')))
  <script src="{{ asset('mazer/assets/extensions/apexcharts/apexcharts.min.js') }}"></script>
  {{-- Set locale ID (jika bundle locale tersedia) --}}
  <script>
    if (window.Apex) {
      window.Apex.chart = window.Apex.chart || {};
      window.Apex.chart.locales = (window.Apex.chart.locales || []).concat([{
        name: 'id',
        options: { toolbar: { exportToSVG: 'SVG', exportToPNG: 'PNG', exportToCSV: 'CSV' } }
      }]);
      window.Apex.chart.defaultLocale = 'id';
    }
  </script>
@endif

{{-- Datatables (jQuery + Bootstrap 5 skin) --}}
@if (file_exists(public_path('mazer/assets/extensions/datatables.net/js/jquery.dataTables.min.js')))
  <script src="{{ asset('mazer/assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
@endif
@if (file_exists(public_path('mazer/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js')))
  <script src="{{ asset('mazer/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
@endif

{{-- Simple-DataTables (versi tanpa jQuery) --}}
@if (file_exists(public_path('mazer/assets/extensions/simple-datatables/umd/simple-datatables.js')))
  <script src="{{ asset('mazer/assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
@endif

{{-- Flatpickr + locale ID + plugin confirmDate (jika ada) --}}
@if (file_exists(public_path('mazer/assets/extensions/flatpickr/flatpickr.min.js')))
  <script src="{{ asset('mazer/assets/extensions/flatpickr/flatpickr.min.js') }}"></script>
  @if (file_exists(public_path('mazer/assets/extensions/flatpickr/l10n/id.js')))
    <script src="{{ asset('mazer/assets/extensions/flatpickr/l10n/id.js') }}"></script>
    <script>try{flatpickr.localize(flatpickr.l10ns.id);}catch(e){}</script>
  @endif
  @if (file_exists(public_path('mazer/assets/extensions/flatpickr/plugins/confirmDate/confirmDate.js')))
    <script src="{{ asset('mazer/assets/extensions/flatpickr/plugins/confirmDate/confirmDate.js') }}"></script>
  @endif
@endif

{{-- Choices.js (select yang cakep) --}}
@if (file_exists(public_path('mazer/assets/extensions/choices.js/public/assets/scripts/choices.min.js')))
  <script src="{{ asset('mazer/assets/extensions/choices.js/public/assets/scripts/choices.min.js') }}"></script>
@endif

{{-- FilePond + plugins umum --}}
@if (file_exists(public_path('mazer/assets/extensions/filepond/filepond.min.js')))
  <script src="{{ asset('mazer/assets/extensions/filepond/filepond.min.js') }}"></script>
@endif
@foreach ([
  'filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js',
  'filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js',
  'filepond-plugin-image-preview/filepond-plugin-image-preview.min.js',
  'filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js',
  'filepond-plugin-image-crop/filepond-plugin-image-crop.min.js',
  'filepond-plugin-image-resize/filepond-plugin-image-resize.min.js',
  'filepond-plugin-image-filter/filepond-plugin-image-filter.min.js',
] as $pond)
  @if (file_exists(public_path("mazer/assets/extensions/$pond")))
    <script src="{{ asset("mazer/assets/extensions/$pond") }}"></script>
  @endif
@endforeach

{{-- Summernote, Quill, TinyMCE (WYSIWYG) --}}
@if (file_exists(public_path('mazer/assets/extensions/summernote/summernote-lite.min.js')))
  <script src="{{ asset('mazer/assets/extensions/summernote/summernote-lite.min.js') }}"></script>
  <script src="{{ asset('mazer/assets/extensions/summernote/summernote-lite.min.js') }}"></script>
  @if (file_exists(public_path('mazer/assets/extensions/summernote/lang/summernote-id-ID.min.js')))
    <script src="{{ asset('mazer/assets/extensions/summernote/lang/summernote-id-ID.min.js') }}"></script>
  @endif
@endif
@if (file_exists(public_path('mazer/assets/extensions/quill/quill.min.js')))
  <script src="{{ asset('mazer/assets/extensions/quill/quill.min.js') }}"></script>
@endif
@if (file_exists(public_path('mazer/assets/extensions/tinymce/tinymce.min.js')))
  <script src="{{ asset('mazer/assets/extensions/tinymce/tinymce.min.js') }}"></script>
@endif

{{-- SweetAlert2 & Toastify --}}
@if (file_exists(public_path('mazer/assets/extensions/sweetalert2/sweetalert2.min.js')))
  <script src="{{ asset('mazer/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
  <script src="{{ asset('mazer/assets/extensions/sweetalert2/sweetalert2.all.js') }}"></script>
  <script src="{{ asset('mazer/assets/extensions/sweetalert2/sweetalert2.js') }}"></script>
  <script src="{{ asset('mazer/assets/extensions/sweetalert2/sweetalert2.all.min.js') }}"></script>
@endif
@if (file_exists(public_path('mazer/assets/extensions/toastify-js/src/toastify.js')))
  <script src="{{ asset('mazer/assets/extensions/toastify-js/src/toastify.js') }}"></script>
@endif

{{-- Maps --}}
@if (file_exists(public_path('mazer/assets/extensions/jsvectormap/js/jsvectormap.min.js')))
  <script src="{{ asset('mazer/assets/extensions/jsvectormap/js/jsvectormap.min.js') }}"></script>
  @if (file_exists(public_path('mazer/assets/extensions/jsvectormap/maps/world.js')))
    <script src="{{ asset('mazer/assets/extensions/jsvectormap/maps/world.js') }}"></script>
  @endif
@endif

{{-- Lain-lain: Dragula, Rater.js --}}
@if (file_exists(public_path('mazer/assets/extensions/dragula/dragula.min.js')))
  <script src="{{ asset('mazer/assets/extensions/dragula/dragula.min.js') }}"></script>
@endif
@if (file_exists(public_path('mazer/assets/extensions/rater-js/index.js')))
  <script src="{{ asset('mazer/assets/extensions/rater-js/index.js') }}"></script>
@endif

{{-- Parsley (validasi form) + i18n ID --}}
@if (file_exists(public_path('mazer/assets/extensions/parsleyjs/parsley.min.js')))
  <script src="{{ asset('mazer/assets/extensions/parsleyjs/parsley.min.js') }}"></script>
  @if (file_exists(public_path('mazer/assets/extensions/parsleyjs/i18n/id.js')))
    <script src="{{ asset('mazer/assets/extensions/parsleyjs/i18n/id.js') }}"></script>
    <script>try{window.Parsley.addMessages('id', window.Parsley.getMessages('id')); window.Parsley.setLocale('id');}catch(e){}</script>
  @endif
@endif

{{-- Helper init ringan agar plugin siap pakai secara default --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Datatable (jQuery) auto-init jika ada table[data-datatable]
    if (window.jQuery && jQuery().DataTable) {
      document.querySelectorAll('table[data-datatable]').forEach(function (el) {
        jQuery(el).DataTable();
      });
    }

    // Simple-DataTable auto-init jika ada table[data-simple-datatable]
    if (window.simpleDatatables) {
      document.querySelectorAll('table[data-simple-datatable]').forEach(function (el) {
        new window.simpleDatatables.DataTable(el);
      });
    }

    // Flatpickr auto-init
    if (window.flatpickr) {
      document.querySelectorAll('input[data-flatpickr]').forEach(function (el) {
        window.flatpickr(el, {dateFormat: 'Y-m-d'});
      });
    }

    // Choices auto-init
    if (window.Choices) {
      document.querySelectorAll('select[data-choices]').forEach(function (el) {
        new Choices(el, { allowSearch: true, shouldSort: false });
      });
    }

    // Filepond auto-init
    if (window.FilePond) {
      document.querySelectorAll('input[data-filepond]').forEach(function (el) {
        window.FilePond.create(el);
      });
    }

    // Summernote auto-init
    if (window.jQuery && jQuery.fn.summernote) {
      document.querySelectorAll('[data-summernote]').forEach(function (el) {
        jQuery(el).summernote({ height: 200, lang: 'id-ID' });
      });
    }
  });
</script>

{{-- SELALU paksa LIGHT di awal muat halaman --}}
<script>
    (function () {
    try {
        var KEY = 'theme';
        localStorage.setItem(KEY, 'light'); // timpa preferensi
        document.documentElement.classList.remove('theme-dark');
        document.documentElement.setAttribute('data-bs-theme', 'light');
    } catch (e) {}
    })();
</script>

{{-- Auto Loading Button untuk semua form submit --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('Auto form loading initialized'); // Debug log
    
    // Handler untuk semua form submit
    document.addEventListener('submit', function(e) {
        console.log('Form submitted!', e.target); // Debug log
        
        const form = e.target;
        
        // Cari tombol submit dalam form
        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        console.log('Found submit buttons:', submitButtons.length); // Debug log
        
        submitButtons.forEach(function(btn) {
            console.log('Processing button:', btn); // Debug log
            
            // Simpan teks asli
            const originalText = btn.textContent || btn.value;
            const originalHtml = btn.innerHTML;
            
            // Disable button
            btn.disabled = true;
            
            // Tambah class loading Bootstrap
            btn.classList.add('btn-loading');
            
            // Ganti teks dengan spinner
            if (btn.tagName.toLowerCase() === 'button') {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
                console.log('Button text changed to loading'); // Debug log
            } else {
                btn.value = 'Memproses...';
            }
            
            // Reset setelah 15 detik (fallback jika redirect gagal)
            setTimeout(function() {
                console.log('Resetting button after timeout'); // Debug log
                btn.disabled = false;
                btn.classList.remove('btn-loading');
                if (btn.tagName.toLowerCase() === 'button') {
                    btn.innerHTML = originalHtml;
                } else {
                    btn.value = originalText;
                }
            }, 15000);
        });
    });
    
    // Handler khusus untuk tombol dengan data-loading-text
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('button[data-loading-text]');
        if (btn && btn.form) {
            const loadingText = btn.getAttribute('data-loading-text');
            
            // Set custom loading text jika ada (akan override yang di submit handler)
            setTimeout(function() {
                if (btn.disabled) {
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + loadingText;
                }
            }, 10);
        }
    });
    
    // Alternative: Direct click handler untuk button submit (backup method)
    document.addEventListener('click', function(e) {
        if (e.target.matches('button[type="submit"], input[type="submit"]')) {
            const btn = e.target;
            const form = btn.closest('form');
            
            if (form && !btn.disabled) {
                console.log('Direct button click detected'); // Debug log
                
                // Small delay to let form validation run first
                setTimeout(function() {
                    if (!btn.disabled) { // Only proceed if not disabled by validation
                        const originalHtml = btn.innerHTML;
                        
                        btn.disabled = true;
                        btn.classList.add('btn-loading');
                        
                        if (btn.tagName.toLowerCase() === 'button') {
                            const loadingText = btn.getAttribute('data-loading-text') || 'Memproses...';
                            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + loadingText;
                        }
                        
                        // Reset after timeout
                        setTimeout(function() {
                            btn.disabled = false;
                            btn.classList.remove('btn-loading');
                            btn.innerHTML = originalHtml;
                        }, 15000);
                    }
                }, 100);
            }
        }
    });
});
</script>

{{-- SELALU paksa LIGHT di awal muat halaman --}}
<script>
    (function () {
    try {
        var KEY = 'theme';
        localStorage.setItem(KEY, 'light');              // timpa preferensi
        document.documentElement.classList.remove('theme-dark');
        document.documentElement.setAttribute('data-bs-theme', 'light');
    } catch (e) {}
    })();
</script>