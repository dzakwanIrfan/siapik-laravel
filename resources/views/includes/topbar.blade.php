<header>
    <nav class="navbar navbar-expand navbar-light navbar-top">
        <div class="container-fluid">
            {{-- Burger untuk toggle sidebar --}}
            <a href="#" class="burger-btn d-block">
                <i class="bi bi-justify fs-3"></i>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                {{-- Slot kiri (opsional) --}}
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @yield('navbar_left')
                </ul>

                {{-- Ikon kanan default --}}
                <ul class="navbar-nav ms-auto mb-lg-0 align-items-center">
                    {{-- Mail --}}
                    <li class="nav-item dropdown me-1">
                        <a class="nav-link dropdown-toggle text-gray-600" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-envelope bi-sub fs-4"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg-end">
                            <li><h6 class="dropdown-header">Mail</h6></li>
                            <li><a class="dropdown-item" href="#">No new mail</a></li>
                        </ul>
                    </li>

                    {{-- Notifications --}}
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle text-gray-600 position-relative" href="#" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                            <i class="bi bi-bell bi-sub fs-4"></i>
                        <span class="badge badge-notification bg-danger">7</span>
                        </a>
                        <ul class="dropdown-menu dropdown-center dropdown-menu-sm-end notification-dropdown">
                            <li class="dropdown-header"><h6>Notifications</h6></li>
                            <li class="dropdown-item notification-item">
                                <a class="d-flex align-items-center" href="#">
                                <div class="notification-icon bg-primary"><i class="bi bi-cart-check"></i></div>
                                <div class="notification-text ms-4">
                                    <p class="notification-title font-bold mb-0">Successfully check out</p>
                                    <p class="notification-subtitle font-thin text-sm mb-0">Order ID #256</p>
                                </div>
                                </a>
                            </li>
                            <li class="dropdown-item notification-item">
                                <a class="d-flex align-items-center" href="#">
                                <div class="notification-icon bg-success"><i class="bi bi-file-earmark-check"></i></div>
                                <div class="notification-text ms-4">
                                    <p class="notification-title font-bold mb-0">Homework submitted</p>
                                    <p class="notification-subtitle font-thin text-sm mb-0">Algebra math homework</p>
                                </div>
                                </a>
                            </li>
                            <li><p class="text-center py-2 mb-0"><a href="#">See all notification</a></p></li>
                        </ul>
                    </li>

                    {{-- User / Auth --}}
                    @auth
                        @php($user = auth()->user())
                        <li class="nav-item dropdown">
                        <a href="#" class="nav-link" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-menu d-flex">
                            <div class="user-name text-end me-3">
                                <h6 class="mb-0 text-gray-600">{{ $user->txtFullName }}</h6>
                                <p class="mb-0 text-sm text-gray-600">
                                {{ $user->getRoleNames()->first() ?? '-' }}
                                </p>
                            </div>
                            <div class="user-img d-flex align-items-center">
                                <div class="avatar avatar-md">
                                    <img src="{{ asset('mazer/assets/compiled/jpg/1.jpg') }}" alt="avatar">
                                </div>
                            </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 11rem;">
                            <li><h6 class="dropdown-header">Hello, {{ $user->txtFullName }}</h6></li>
                            <li><a class="dropdown-item" href="{{ route('profile', $user->id) }}"><i class="icon-mid bi bi-person me-2"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-gear me-2"></i> Settings</a></li>
                            <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-wallet me-2"></i> Wallet</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                {{-- tombol mengacu ke form logout tersembunyi --}}
                                <button type="submit" form="logoutForm" class="dropdown-item">
                                    <i class="icon-mid bi bi-box-arrow-left me-2"></i> Logout
                                </button>
                            </li>
                        </ul>
                        </li>
                    @endauth

                    @guest
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link">
                                <i class="icon-mid bi bi-box-arrow-in-right me-2"></i> Login
                            </a>
                        </li>
                    @endguest

                    {{-- Slot kanan (opsional) --}}
                    @yield('navbar_right')
                </ul>
            </div>
        </div>
    </nav>

    {{-- Hidden logout form (di luar dropdown, tidak bersarang) --}}
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</header>
