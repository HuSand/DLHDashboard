<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --bg-main: #F9FAFB;
            --bg-sidebar: #FFFFFF;
            --bg-card: #FFFFFF;
            --color-text-primary: #111827;
            --color-text-secondary: #6B7280;
            --color-border: #E5E7EB;
            --border-radius: 0.75rem;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --color-primary: #4F46E5;
            --color-green: #10B981;
            --color-purple: #8B5CF6;
            --color-orange: #F59E0B;
        }
        body { font-family: 'Figtree', sans-serif; background-color: var(--bg-main); color: var(--color-text-primary); font-size: 14px; }
        .wrapper { display: flex; }
        .sidebar { width: 250px; background-color: var(--bg-sidebar); padding: 1.5rem; display: flex; flex-direction: column; flex-shrink: 0; height: 100vh; position: sticky; top: 0; border-right: 1px solid var(--color-border); }
        .sidebar-header { margin-bottom: 2rem; font-size: 1.5rem; font-weight: 700; }
        .sidebar .nav-link { display: flex; align-items: center; padding: 0.75rem 1rem; border-radius: 0.5rem; color: var(--color-text-secondary); font-weight: 500; margin-bottom: 0.25rem; }
        .sidebar .nav-link i { font-size: 1.2rem; margin-right: 1rem; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { background-color: var(--color-primary); color: #FFFFFF; }
        .main-content { flex-grow: 1; padding: 2rem; }
        .card { background-color: var(--bg-card); border-radius: var(--border-radius); padding: 1.5rem; box-shadow: var(--shadow); border: 1px solid var(--color-border); transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); }
        .kpi-card.kpi-blue { background-image: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%); color: #3B82F6; }
        .kpi-card.kpi-green { background-image: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); color: var(--color-green); }
        .kpi-card.kpi-purple { background-image: linear-gradient(135deg, #F5F3FF 0%, #EDE9FE 100%); color: var(--color-purple); }
        .kpi-card.kpi-orange { background-image: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%); color: var(--color-orange); }
        .scrollable-list { max-height: 400px; overflow-y: auto; padding-right: 10px; }
        .scrollable-list .list-group-item { transition: background-color 0.2s ease; border-radius: 0.5rem !important; margin-bottom: 0.5rem; border: none !important; background-color: #f9fafb; }
        .scrollable-list .list-group-item:hover { background-color: #f3f4f6; }
        .scrollable-list::-webkit-scrollbar { width: 5px; }
        .scrollable-list::-webkit-scrollbar-track { background: #f1f1f1; }
        .scrollable-list::-webkit-scrollbar-thumb { background: #ccc; border-radius: 5px; }
        .scrollable-list::-webkit-scrollbar-thumb:hover { background: #aaa; }
        .nav-tabs { border-bottom: 1px solid var(--color-border); }
        .nav-tabs .nav-link { border: none; color: var(--color-text-secondary); font-weight: 600; }
        .nav-tabs .nav-link.active { color: var(--color-primary); border-bottom: 2px solid var(--color-primary); background-color: transparent; }
    </style>
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <div class="sidebar-header"><a href="{{ route('dashboard') }}" class="d-flex align-items-center text-dark text-decoration-none"><i class="bi bi-box-seam-fill me-2" style="color: var(--color-primary);"></i>OFSPACE.CO</a></div>
            <nav class="d-flex flex-column justify-content-between flex-grow-1">
                <div>
                    <p class="small text-secondary text-uppercase fw-bold px-3">Main Menu</p>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}"><i class="bi bi-house-door"></i> Home</a></li>
                        <li class="nav-item"><a href="{{ route('mahasiswa.index') }}" class="nav-link {{ Route::is('mahasiswa.index') ? 'active' : '' }}"><i class="bi bi-people"></i> Mahasiswa</a></li>
                        <li class="nav-item"><a href="{{ route('matakuliah.index') }}" class="nav-link {{ Route::is('matakuliah.index') ? 'active' : '' }}"><i class="bi bi-journal-text"></i> Mata Kuliah</a></li>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
