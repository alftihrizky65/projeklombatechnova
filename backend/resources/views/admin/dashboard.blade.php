@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<!-- Stat Cards -->
<div class="grid grid-4 mb-6">
    <div class="card stat-card">
        <span class="stat-icon">👥</span>
        <div class="stat-label">Total Users</div>
        <div class="stat-value">{{ number_format($totalUsers) }}</div>
        <div class="stat-sub">+{{ $newUsersToday }} hari ini</div>
    </div>
    <div class="card stat-card">
        <span class="stat-icon">📚</span>
        <div class="stat-label">Total Konten</div>
        <div class="stat-value">{{ $totalContent }}</div>
        <div class="stat-sub">Tutorial + Kamus + Kuis</div>
    </div>
    <div class="card stat-card">
        <span class="stat-icon">🎯</span>
        <div class="stat-label">Akurasi AI</div>
        <div class="stat-value">{{ $aiAccuracy }}%</div>
        <div class="stat-sub">Avg {{ $avgResponseTime }}ms response</div>
    </div>
    <div class="card stat-card">
        <span class="stat-icon">🔔</span>
        <div class="stat-label">System Status</div>
        <div class="stat-value" id="sys-status" style="font-size:16px;">Checking...</div>
        <div class="stat-sub" id="sys-detail">—</div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-2 mb-6">
    <div class="card">
        <div class="card-title">📈 Pertumbuhan User (30 Hari)</div>
        <div class="chart-container">
            <canvas id="userGrowthChart"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-title">🎯 Interaksi Terpopuler</div>
        <div class="chart-container">
            <canvas id="topSignsChart"></canvas>
        </div>
    </div>
</div>

<!-- Bottom Row -->
<div class="grid grid-2">
    <div class="card">
        <div class="card-title">👤 User Terbaru</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Nama</th><th>Level</th><th>XP</th><th>Bergabung</th></tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $u)
                    <tr>
                        <td>
                            <div style="font-weight:600">{{ $u->name }}</div>
                            <div class="text-xs text-dim">{{ $u->email }}</div>
                        </td>
                        <td><span class="badge badge-accent">Lv.{{ $u->level }}</span></td>
                        <td class="font-mono text-accent">{{ number_format($u->xp) }}</td>
                        <td class="text-dim text-xs">{{ $u->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-dim" style="text-align:center;padding:24px">Belum ada user terdaftar</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-title">🔧 Log Sistem Terbaru</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Level</th><th>Pesan</th><th>Waktu</th></tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                    <tr>
                        <td>
                            @if($log->level === 'error' || $log->level === 'critical')
                                <span class="badge badge-danger">{{ $log->level }}</span>
                            @elseif($log->level === 'warning')
                                <span class="badge badge-warning">{{ $log->level }}</span>
                            @else
                                <span class="badge badge-info">{{ $log->level }}</span>
                            @endif
                        </td>
                        <td class="text-sm">{{ Str::limit($log->message, 50) }}</td>
                        <td class="text-dim text-xs">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-dim" style="text-align:center;padding:24px">Tidak ada log</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const chartDefaults = {
    color: '#72727e',
    borderColor: 'rgba(255,255,255,0.06)',
    font: { family: "'Inter', sans-serif" }
};

Chart.defaults.color = chartDefaults.color;
Chart.defaults.borderColor = chartDefaults.borderColor;
Chart.defaults.font.family = chartDefaults.font.family;

// User Growth Chart
fetch('/admin/api/user-growth')
    .then(r => r.json())
    .then(data => {
        new Chart(document.getElementById('userGrowthChart'), {
            type: 'line',
            data: {
                labels: data.map(d => d.date),
                datasets: [{
                    label: 'User Baru',
                    data: data.map(d => d.count),
                    borderColor: '#00ff88',
                    backgroundColor: 'rgba(0,255,136,0.08)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#00ff88',
                    pointBorderColor: '#0f0f11',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { maxTicksLimit: 8, font: { size: 10 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.03)' },
                        ticks: { font: { size: 10 }, stepSize: 1 }
                    }
                }
            }
        });
    });

// Top Signs Bar Chart
const topSigns = @json($topSigns);
if (topSigns.length > 0) {
    new Chart(document.getElementById('topSignsChart'), {
        type: 'bar',
        data: {
            labels: topSigns.map(s => s.sign_detected),
            datasets: [{
                label: 'Interaksi',
                data: topSigns.map(s => s.total),
                backgroundColor: topSigns.map((_, i) =>
                    `rgba(0, ${180 + i * 8}, ${100 + i * 10}, ${0.7 - i * 0.04})`
                ),
                borderColor: '#00ff88',
                borderWidth: 1,
                borderRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,0.03)' },
                    ticks: { font: { size: 10 } }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { family: "'Space Mono'", size: 11 } }
                }
            }
        }
    });
} else {
    document.getElementById('topSignsChart').parentElement.innerHTML =
        '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#72727e;font-size:13px">Belum ada data interaksi AI</div>';
}

// System Health
fetch('/admin/api/system-health')
    .then(r => r.json())
    .then(d => {
        const el = document.getElementById('sys-status');
        const detail = document.getElementById('sys-detail');
        const allGood = d.laravel && d.database;
        el.innerHTML = allGood
            ? '<span class="status-dot online"></span> All Systems OK'
            : '<span class="status-dot offline"></span> Issue Detected';
        detail.textContent = `PHP ${d.php_version} · ${d.memory} RAM`;
    })
    .catch(() => {
        document.getElementById('sys-status').innerHTML = '<span class="status-dot offline"></span> Unreachable';
    });
</script>
@endsection
