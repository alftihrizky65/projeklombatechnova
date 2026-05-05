@extends('layouts.admin')
@section('title', 'Reports')

@section('content')
<div class="grid grid-4 mb-6">
    <div class="card stat-card">
        <span class="stat-icon">👥</span>
        <div class="stat-label">Total Users</div>
        <div class="stat-value">{{ number_format($totalUsers) }}</div>
    </div>
    <div class="card stat-card">
        <span class="stat-icon">🎮</span>
        <div class="stat-label">User Aktif</div>
        <div class="stat-value">{{ number_format($activeUsers) }}</div>
        <div class="stat-sub">XP > 0</div>
    </div>
    <div class="card stat-card">
        <span class="stat-icon">⭐</span>
        <div class="stat-label">Avg Level</div>
        <div class="stat-value">{{ $avgLevel }}</div>
    </div>
    <div class="card stat-card">
        <span class="stat-icon">🤖</span>
        <div class="stat-label">Total AI Calls</div>
        <div class="stat-value">{{ number_format($totalAiCalls) }}</div>
    </div>
</div>

<div class="grid grid-2 mb-6">
    <div class="card">
        <div class="card-title">📈 Pertumbuhan User (30 Hari)</div>
        <div class="chart-container"><canvas id="growthChart"></canvas></div>
    </div>
    <div class="card">
        <div class="card-title">📊 Distribusi Level User</div>
        <div class="chart-container"><canvas id="levelChart"></canvas></div>
    </div>
</div>

<div class="card">
    <div class="card-title">🔥 Isyarat Terpopuler</div>
    <div class="grid grid-2" style="gap:12px">
        @forelse($topSigns as $i => $sign)
        <div style="display:flex;align-items:center;gap:12px;padding:12px;background:rgba(255,255,255,0.02);border-radius:4px;border:1px solid rgba(255,255,255,0.04)">
            <div class="font-mono text-accent" style="font-size:20px;font-weight:700;width:36px;text-align:center">{{ $i+1 }}</div>
            <div style="flex:1">
                <div class="font-mono" style="font-weight:600">{{ $sign->sign_detected }}</div>
                <div class="text-dim text-xs">{{ $sign->total }} interaksi</div>
            </div>
            <div style="width:120px;height:8px;background:rgba(255,255,255,0.06);border-radius:2px;overflow:hidden">
                <div style="width:{{ $topSigns->count() > 0 ? ($sign->total / $topSigns->first()->total * 100) : 0 }}%;height:100%;background:linear-gradient(90deg,#00ff88,#00cc6a);border-radius:2px"></div>
            </div>
        </div>
        @empty
        <div class="text-dim" style="padding:24px;text-align:center;grid-column:1/-1">Belum ada data</div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
fetch('/admin/api/user-growth').then(r=>r.json()).then(data=>{
    new Chart(document.getElementById('growthChart'),{
        type:'line',
        data:{
            labels:data.map(d=>d.date),
            datasets:[{
                label:'User Baru',data:data.map(d=>d.count),
                borderColor:'#00ff88',backgroundColor:'rgba(0,255,136,0.08)',
                fill:true,tension:.4,borderWidth:2,
                pointBackgroundColor:'#00ff88',pointBorderColor:'#0f0f11',pointBorderWidth:2,pointRadius:3
            }]
        },
        options:{
            responsive:true,maintainAspectRatio:false,
            plugins:{legend:{display:false}},
            scales:{
                x:{grid:{display:false},ticks:{maxTicksLimit:8,font:{size:10}}},
                y:{beginAtZero:true,grid:{color:'rgba(255,255,255,0.03)'},ticks:{stepSize:1,font:{size:10}}}
            }
        }
    });
});

const levelData = @json($levelDistribution);
if(levelData.length > 0){
    new Chart(document.getElementById('levelChart'),{
        type:'doughnut',
        data:{
            labels:levelData.map(d=>'Level '+d.level),
            datasets:[{
                data:levelData.map(d=>d.total),
                backgroundColor:levelData.map((_,i)=>`hsl(${150+i*25},80%,${45+i*5}%)`),
                borderColor:'#16161a',borderWidth:2
            }]
        },
        options:{
            responsive:true,maintainAspectRatio:false,
            plugins:{legend:{position:'right',labels:{font:{size:11},color:'#72727e',usePointStyle:true,pointStyle:'rect',padding:12}}}
        }
    });
} else {
    document.getElementById('levelChart').parentElement.innerHTML='<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#72727e">Belum ada data</div>';
}
</script>
@endsection
