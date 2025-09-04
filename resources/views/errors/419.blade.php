<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expired | SIAPIK</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.ico') }}">
  <link rel="stylesheet" crossorigin href="{{ asset('mazer/assets/compiled/css/app.css') }}">
  <link rel="stylesheet" crossorigin href="{{ asset('mazer/assets/compiled/css/error.css') }}">
</head>

<body>
    <script src="{{ asset('mazer/assets/static/js/initTheme.js') }}"></script>
    <div id="error">
        <div class="error-page container">
            <div class="col-md-8 col-12 offset-md-2">
                <div class="text-center">
                    <img class="img-error" src="{{ asset('mazer/assets/compiled/svg/error-403.svg') }}" alt="Forbidden">
                    <h1 class="error-title">Expired</h1>
                    <p class="fs-5 text-gray-600">Your session has expired.</p>
                    <a href="{{ route('login') }}" class="btn btn-lg btn-outline-primary mt-3">Login</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>