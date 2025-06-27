<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <div class="sidebar-header"><a href="#" class="d-flex align-items-center text-dark"><i class="bi bi-box-seam-fill me-2" style="color: var(--color-primary);"></i>OFSPACE.CO</a></div>
            <nav class="d-flex flex-column justify-content-between flex-grow-1">
                <div>
                    <p class="sidebar-menu-title">Main Menu</p>
                    <ul class="nav flex-column">
                        {{-- Link Home kita arahkan ke route 'dashboard' --}}
                        <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}"><i class="bi bi-house-door"></i> Home</a></li>

                        {{-- Link Mahasiswa kita arahkan ke route 'mahasiswa.index' --}}
                        <li class="nav-item"><a href="{{ route('mahasiswa.index') }}" class="nav-link {{ Route::is('mahasiswa.index') ? 'active' : '' }}"><i class="bi bi-people"></i> Mahasiswa</a></li>

                        {{-- Link Mata Kuliah kita arahkan ke route 'matakuliah.index' --}}
                        <li class="nav-item"><a href="{{ route('matakuliah.index') }}" class="nav-link {{ Route::is('matakuliah.index') ? 'active' : '' }}"><i class="bi bi-journal-text"></i> Mata Kuliah <span class="badge ms-auto">12</span></a></li>
                    </ul>
                    <p class="sidebar-menu-title">Account Management</p>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-gear"></i> Setting</a></li>
                    </ul>
                </div>
                <div><ul class="nav flex-column"><li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-box-arrow-left"></i> Log out</a></li></ul></div>
            </nav>
        </aside>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @stack('scripts')
</body>
</html>
