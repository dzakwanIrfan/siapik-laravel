<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Persuratan</title>
    <link rel="shortcut icon" href="data:image/svg+xml,%3csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2033%2034'%20fill-rule='evenodd'%20stroke-linejoin='round'%20stroke-miterlimit='2'%20xmlns:v='https://vecta.io/nano'%3e%3cpath%20d='M3%2027.472c0%204.409%206.18%205.552%2013.5%205.552%207.281%200%2013.5-1.103%2013.5-5.513s-6.179-5.552-13.5-5.552c-7.281%200-13.5%201.103-13.5%205.513z'%20fill='%23435ebe'%20fill-rule='nonzero'/%3e%3ccircle%20cx='16.5'%20cy='8.8'%20r='8.8'%20fill='%2341bbdd'/%3e%3c/svg%3e" type="image/x-icon">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC" type="image/png">
    
    <!-- CSS -->
    <link rel="stylesheet" crossorigin href="{{ asset('mazer/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" crossorigin href="{{ asset('mazer/assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" crossorigin href="{{ asset('mazer/assets/compiled/css/auth.css') }}">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="{{ asset('mazer/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
</head>

<body>
    <script src="{{ asset('mazer/assets/static/js/initTheme.js') }}"></script>
    
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo">
                        <a href="index.html" class="d-flex gap-2">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:48px">
                        </a>
                    </div>
                    <h1 class="auth-title">Log in.</h1>
                    <p class="auth-subtitle mb-4">Log in dengan email atau NIM dan password yang telah terdaftar.</p>

                    <form method="POST" action="{{ route('login.attempt') }}" id="loginForm" data-parsley-validate>
                        @csrf
                        
                        <!-- Email/NIM Field -->
                        <div class="form-group position-relative has-icon-left mb-3">
                            <input type="text" 
                                   name="login" 
                                   id="login"
                                   class="form-control" 
                                   placeholder="Email atau NIM"
                                   required
                                   data-parsley-required-message="Email atau NIM wajib diisi"
                                   data-parsley-trigger="focusout"
                                   autocomplete="username">
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        
                        <!-- Password Field dengan Toggle Visibility -->
                        <div class="form-group position-relative has-icon-left mb-3">
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="form-control pe-5" 
                                   placeholder="Password"
                                   required
                                   minlength="6"
                                   data-parsley-required-message="Password wajib diisi"
                                   data-parsley-minlength-message="Password minimal 6 karakter"
                                   data-parsley-trigger="focusout"
                                   autocomplete="current-password">
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <i class="bi bi-eye position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer text-muted" 
                               id="togglePassword" 
                               title="Tampilkan/Sembunyikan Password"
                               style="z-index: 10;"></i>
                        </div>
                        
                        <!-- Remember Me -->
                        <div class="form-check form-check-lg d-flex align-items-end">
                            <input class="form-check-input me-2" type="checkbox" value="1" name="remember" id="flexCheckDefault">
                            <label class="form-check-label text-gray-600" for="flexCheckDefault">
                                Keep me logged in
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button class="btn btn-primary btn-block shadow-lg mt-5 position-relative" type="submit" id="loginBtn">
                            <span id="btnText">Log in</span>
                            <span class="spinner-border spinner-border-sm d-none" id="btnSpinner" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right">
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('mazer/assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('mazer/assets/extensions/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ asset('mazer/assets/extensions/parsleyjs/i18n/id.js') }}"></script>
    <script src="{{ asset('mazer/assets/extensions/sweetalert2/sweetalert2.all.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Parsley with Indonesian language
            Parsley.setLocale('id');
            
            // Custom Parsley validator for email or NIM format
            Parsley.addValidator('loginformat', {
                validateString: function(value) {
                    // Check if it's an email or numeric (NIM)
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    const nimRegex = /^\d+$/;
                    return emailRegex.test(value) || nimRegex.test(value);
                },
                messages: {
                    id: 'Format harus berupa email yang valid atau NIM',
                    en: 'Must be a valid email or NIM (numbers only)'
                }
            });
            
            // Add custom validator to login field
            $('#login').attr('data-parsley-loginformat', '');
            
            // Password visibility toggle
            $('#togglePassword').click(function() {
                const passwordField = $('#password');
                const passwordFieldType = passwordField.attr('type');
                
                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });
            
            // Form submission with loading state and SweetAlert
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validate form
                if (!$(this).parsley().isValid()) {
                    return;
                }
                
                // Show loading state
                const loginBtn = $('#loginBtn');
                const btnText = $('#btnText');
                const btnSpinner = $('#btnSpinner');
                
                loginBtn.prop('disabled', true);
                btnText.addClass('d-none');
                btnSpinner.removeClass('d-none');
                
                // Simulate loading for better UX (remove this in production)
                setTimeout(() => {
                    // Submit form
                    this.submit();
                }, 1000);
            });
            
            // Show success message if login successful (you can trigger this from controller)
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif
            
            // Show error message if login failed
            @if(session('error') || $errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal!',
                    text: '{{ session('error') ?? $errors->first() }}',
                    confirmButtonText: 'Coba Lagi',
                    confirmButtonColor: '#435ebe'
                });
            @endif
            
            // Auto-focus on login field when page loads
            $('#login').focus();
            
            // Enhanced form interactions
            $('.form-control').on('focus', function() {
                $(this).addClass('border-primary');
            }).on('blur', function() {
                $(this).removeClass('border-primary');
            });
            
            // Keyboard shortcuts
            $(document).keydown(function(e) {
                // Alt + L to focus login field
                if (e.altKey && e.which === 76) {
                    e.preventDefault();
                    $('#login').focus();
                }
                // Alt + P to focus password field
                if (e.altKey && e.which === 80) {
                    e.preventDefault();
                    $('#password').focus();
                }
            });
            
            // Clear validation errors on input
            $('.form-control').on('input', function() {
                if ($(this).hasClass('parsley-error')) {
                    $(this).parsley().reset();
                }
                // Add success styling for valid inputs
                if ($(this).parsley().isValid()) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(this).removeClass('is-valid');
                }
            });
        });
    </script>
    <script>
    $(document).ready(function() {
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

        // Show success toast
        @if(Session::has('success'))
            Toast.fire({
                icon: "success",
                title: "{{ Session::get('success') }}"
            });
        @endif

        // Show error toast
        @if(Session::has('error'))
            Toast.fire({
                icon: "error",
                title: "{{ Session::get('error') }}"
            });
        @endif

        // Show info toast
        @if(Session::has('info'))
            Toast.fire({
                icon: "info",
                title: "{{ Session::get('info') }}"
            });
        @endif

        // Show warning toast
        @if(Session::has('warning'))
            Toast.fire({
                icon: "warning",
                title: "{{ Session::get('warning') }}"
            });
        @endif
    });
    </script>
</body>
</html>