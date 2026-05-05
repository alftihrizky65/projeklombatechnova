<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SignEdu Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg: #0f0f11;
            --bg-surface: #16161a;
            --bg-card: rgba(255,255,255,0.03);
            --accent: #00ff88;
            --accent-dim: #00cc6a;
            --accent-glow: rgba(0,255,136,0.12);
            --text: #e0e0e0;
            --text-dim: #72727e;
            --danger: #ff4757;
            --warning: #ffa502;
            --info: #3b82f6;
            --border: rgba(255,255,255,0.06);
            --radius: 6px;
            --sidebar-w: 260px;
            --font-heading: 'Space Mono', monospace;
            --font-body: 'Inter', sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            min-height: 100vh;
            line-height: 1.6;
        }

        a { color: inherit; text-decoration: none; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--bg-surface);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            z-index: 50;
            transition: transform .3s;
        }

        .sidebar-brand {
            padding: 28px 24px 20px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-brand h1 {
            font-family: var(--font-heading);
            font-size: 18px;
            color: var(--accent);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .sidebar-brand span {
            font-family: var(--font-body);
            font-size: 11px;
            color: var(--text-dim);
            letter-spacing: 0;
            text-transform: none;
        }

        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }

        .nav-section {
            font-family: var(--font-heading);
            font-size: 10px;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 16px 12px 8px;
        }

        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px;
            border-radius: var(--radius);
            font-size: 13px;
            font-weight: 500;
            color: var(--text-dim);
            transition: all .2s;
            margin-bottom: 2px;
        }

        .nav-link:hover {
            background: var(--accent-glow);
            color: var(--accent);
        }

        .nav-link.active {
            background: var(--accent-glow);
            color: var(--accent);
            border-left: 3px solid var(--accent);
        }

        .nav-icon { width: 18px; text-align: center; font-size: 15px; }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--border);
            font-size: 12px;
            color: var(--text-dim);
        }

        /* ── Main Content ── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
        }

        .topbar {
            position: sticky; top: 0; z-index: 40;
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 32px;
            background: rgba(15,15,17,0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }

        .topbar-title {
            font-family: var(--font-heading);
            font-size: 16px;
            letter-spacing: 1px;
        }

        .topbar-user {
            display: flex; align-items: center; gap: 12px;
            font-size: 13px;
        }

        .topbar-avatar {
            width: 32px; height: 32px;
            background: var(--accent-glow);
            border: 1px solid var(--accent);
            border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-heading);
            font-size: 12px;
            color: var(--accent);
        }

        .content { padding: 28px 32px; }

        /* ── Cards & Grid ── */
        .grid { display: grid; gap: 20px; }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            backdrop-filter: blur(8px);
            transition: border-color .3s;
        }

        .card:hover { border-color: rgba(0,255,136,0.15); }

        .stat-card { position: relative; overflow: hidden; }
        .stat-card .stat-icon {
            position: absolute; top: 16px; right: 18px;
            font-size: 28px; opacity: 0.15;
        }
        .stat-card .stat-label {
            font-size: 12px; color: var(--text-dim);
            text-transform: uppercase; letter-spacing: 1px;
            font-family: var(--font-heading);
            margin-bottom: 8px;
        }
        .stat-card .stat-value {
            font-size: 32px; font-weight: 700;
            font-family: var(--font-heading);
            color: var(--accent);
        }
        .stat-card .stat-sub {
            font-size: 12px; color: var(--text-dim); margin-top: 6px;
        }

        .card-title {
            font-family: var(--font-heading);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-dim);
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }

        /* ── Table ── */
        .table-wrap { overflow-x: auto; }

        table {
            width: 100%; border-collapse: collapse;
            font-size: 13px;
        }

        th {
            font-family: var(--font-heading);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-dim);
            text-align: left;
            padding: 10px 14px;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
        }

        tr:hover td { background: rgba(255,255,255,0.015); }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            font-family: var(--font-heading);
            letter-spacing: 0.5px;
        }

        .badge-accent { background: var(--accent-glow); color: var(--accent); border: 1px solid rgba(0,255,136,0.25); }
        .badge-info { background: rgba(59,130,246,0.12); color: #60a5fa; border: 1px solid rgba(59,130,246,0.25); }
        .badge-warning { background: rgba(255,165,2,0.12); color: #ffa502; border: 1px solid rgba(255,165,2,0.25); }
        .badge-danger { background: rgba(255,71,87,0.12); color: #ff4757; border: 1px solid rgba(255,71,87,0.25); }
        .badge-success { background: var(--accent-glow); color: var(--accent); border: 1px solid rgba(0,255,136,0.25); }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 18px;
            border-radius: var(--radius);
            font-size: 13px; font-weight: 600;
            font-family: var(--font-body);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all .2s;
            background: transparent;
            color: var(--text);
        }

        .btn-accent {
            background: var(--accent);
            color: #0f0f11;
            border-color: var(--accent);
        }

        .btn-accent:hover { background: var(--accent-dim); }

        .btn-danger {
            background: transparent;
            color: var(--danger);
            border-color: rgba(255,71,87,0.3);
        }

        .btn-danger:hover { background: rgba(255,71,87,0.1); }

        .btn-sm { padding: 5px 12px; font-size: 12px; }

        .btn-ghost {
            background: transparent;
            border-color: transparent;
            color: var(--text-dim);
        }

        .btn-ghost:hover { color: var(--accent); }

        /* ── Forms ── */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 12px;
            color: var(--text-dim);
            margin-bottom: 6px;
            font-family: var(--font-heading);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
            font-size: 14px;
            font-family: var(--font-body);
            transition: border-color .2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2372727e' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
        }

        textarea.form-input { resize: vertical; min-height: 80px; }

        /* ── Alerts ── */
        .alert {
            padding: 12px 18px;
            border-radius: var(--radius);
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid;
        }

        .alert-success { background: var(--accent-glow); color: var(--accent); border-color: rgba(0,255,136,0.2); }
        .alert-error { background: rgba(255,71,87,0.1); color: var(--danger); border-color: rgba(255,71,87,0.2); }

        /* ── Modal ── */
        .modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px);
            z-index: 100;
            display: none; align-items: center; justify-content: center;
        }

        .modal-backdrop.active { display: flex; }

        .modal {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            width: 100%; max-width: 540px;
            max-height: 85vh; overflow-y: auto;
            padding: 28px;
        }

        .modal-title {
            font-family: var(--font-heading);
            font-size: 14px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        /* ── Pagination ── */
        .pagination {
            display: flex; gap: 4px; align-items: center;
            margin-top: 20px; justify-content: center;
        }

        .pagination a, .pagination span {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            border: 1px solid var(--border);
            color: var(--text-dim);
            transition: all .2s;
        }

        .pagination a:hover { border-color: var(--accent); color: var(--accent); }

        .pagination .active span {
            background: var(--accent);
            color: var(--bg);
            border-color: var(--accent);
            font-weight: 700;
        }

        /* ── Search Bar ── */
        .search-bar {
            display: flex; gap: 10px; align-items: center;
            margin-bottom: 20px;
        }

        .search-bar .form-input { max-width: 320px; }

        /* ── Utilities ── */
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .gap-4 { gap: 16px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mt-4 { margin-top: 16px; }
        .text-accent { color: var(--accent); }
        .text-dim { color: var(--text-dim); }
        .text-danger { color: var(--danger); }
        .text-sm { font-size: 13px; }
        .text-xs { font-size: 11px; }
        .font-mono { font-family: var(--font-heading); }
        .w-full { width: 100%; }

        .chart-container { position: relative; height: 280px; }

        /* ── Pixel Decorations ── */
        .pixel-grid {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background-image:
                linear-gradient(rgba(0,255,136,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,255,136,0.02) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .status-dot {
            width: 8px; height: 8px;
            border-radius: 2px;
            display: inline-block;
        }
        .status-dot.online { background: var(--accent); box-shadow: 0 0 6px var(--accent); }
        .status-dot.offline { background: var(--danger); }

        /* ── Checkbox ── */
        .toggle {
            position: relative;
            width: 40px; height: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
            cursor: pointer;
            display: inline-block;
        }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle .slider {
            position: absolute; inset: 2px;
        }
        .toggle .slider::after {
            content: '';
            position: absolute;
            width: 16px; height: 16px;
            background: var(--text-dim);
            border-radius: 2px;
            transition: .2s;
        }
        .toggle input:checked + .slider::after {
            transform: translateX(20px);
            background: var(--accent);
        }

        @media (max-width: 1024px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
            .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
            .content { padding: 20px 16px; }
        }

        .mobile-toggle {
            display: none;
            background: none; border: none;
            color: var(--text); font-size: 20px;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .mobile-toggle { display: block; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="pixel-grid"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h1>⬛ SignEdu</h1>
            <span>Admin Panel v1.0</span>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Main Menu</div>
            <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span> {{ auth()->user()->role === 'user' ? 'My Progress' : 'Dashboard' }}
            </a>
            
            @if(auth()->user()->role === 'user')
            <a href="/admin/content/tutorials" class="nav-link {{ request()->is('admin/content/tutorials') ? 'active' : '' }}">
                <span class="nav-icon">🎬</span> Belajar Isyarat
            </a>
            <a href="/admin/content/quizzes" class="nav-link {{ request()->is('admin/content/quizzes') ? 'active' : '' }}">
                <span class="nav-icon">🎮</span> Ambil Kuis
            </a>
            <a href="/admin/content/dictionary" class="nav-link {{ request()->is('admin/content/dictionary') ? 'active' : '' }}">
                <span class="nav-icon">📖</span> Kamus Isyarat
            </a>
            @else
            <a href="/admin/reports" class="nav-link {{ request()->is('admin/reports') ? 'active' : '' }}">
                <span class="nav-icon">📈</span> Reports
            </a>

            <div class="nav-section">Konten</div>
            <a href="/admin/content/tutorials" class="nav-link {{ request()->is('admin/content/tutorials') ? 'active' : '' }}">
                <span class="nav-icon">🎬</span> Video Tutorial
            </a>
            <a href="/admin/content/dictionary" class="nav-link {{ request()->is('admin/content/dictionary') ? 'active' : '' }}">
                <span class="nav-icon">📖</span> Kamus Isyarat
            </a>
            <a href="/admin/content/quizzes" class="nav-link {{ request()->is('admin/content/quizzes') ? 'active' : '' }}">
                <span class="nav-icon">❓</span> Kuis
            </a>

            <div class="nav-section">Sistem</div>
            <a href="/admin/ai-monitor" class="nav-link {{ request()->is('admin/ai-monitor') ? 'active' : '' }}">
                <span class="nav-icon">🤖</span> AI Monitor
            </a>
            @if(auth()->user()->isAdmin())
            <a href="/admin/users" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                <span class="nav-icon">👥</span> Manajemen User
            </a>
            <a href="/admin/system-logs" class="nav-link {{ request()->is('admin/system-logs') ? 'active' : '' }}">
                <span class="nav-icon">🔧</span> System Logs
            </a>
            @endif
            @endif
        </nav>
        <div class="sidebar-footer">
            <span class="font-mono text-xs">{{ auth()->user()->role }}</span> · {{ auth()->user()->name }}
        </div>
    </aside>

    <!-- Main -->
    <div class="main-wrap">
        <header class="topbar">
            <div class="flex items-center gap-3">
                <button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
                <h2 class="topbar-title">@yield('title', 'Dashboard')</h2>
            </div>
            <div class="topbar-user">
                <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div>
                    <div class="text-sm">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-dim">{{ ucfirst(str_replace('_',' ',auth()->user()->role)) }}</div>
                </div>
                <form action="/admin/logout" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">Logout</button>
                </form>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert alert-success">✓ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">✕ {{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
