@extends('layouts.admin')
@section('title', 'AI Monitor')

@section('content')
<div class="grid grid-4 mb-6">
    <div class="card stat-card">
        <span class="stat-icon">📡</span>
        <div class="stat-label">Total Prediksi</div>
        <div class="stat-value">{{ number_format($totalLogs) }}</div>
    </div>
    <div class="card stat-card">
        <span class="stat-icon">🎯</span>
        <div class="stat-label">Akurasi</div>
        <div class="stat-value">{{ $accuracy }}%</div>
    </div>
    <div class="card stat-card">
        <span class="stat-icon">⚡</span>
        <div class="stat-label">Avg Response</div>
        <div class="stat-value">{{ $avgResponseTime }}ms</div>
    </div>
    <div class="card stat-card">
        <span class="stat-icon">📊</span>
        <div class="stat-label">Hari Ini</div>
        <div class="stat-value">{{ number_format($todayLogs) }}</div>
    </div>
</div>

<div class="grid grid-2 mb-6">
    <div class="card">
        <div class="card-title">📈 Tren Akurasi AI (14 Hari)</div>
        <div class="chart-container"><canvas id="aiChart"></canvas></div>
    </div>
    <div class="card">
        <div class="card-title">🔍 Akurasi Per Isyarat</div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Isyarat</th><th>Total</th><th>Benar</th><th>Confidence</th><th>Response</th></tr></thead>
                <tbody>
                    @forelse($signAccuracy as $s)
                    <tr>
                        <td class="font-mono" style="font-weight:600">{{ $s->sign_detected }}</td>
                        <td>{{ $s->total }}</td>
                        <td class="text-accent">{{ $s->correct }}</td>
                        <td>{{ $s->avg_confidence }}%</td>
                        <td class="text-dim">{{ $s->avg_response }}ms</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-dim" style="text-align:center;padding:24px">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-title">📋 Log Prediksi Terbaru</div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>User</th><th>Isyarat</th><th>Confidence</th><th>Hasil</th><th>Response</th><th>Waktu</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-sm">{{ $log->user->name ?? 'Guest' }}</td>
                    <td class="font-mono" style="font-weight:600">{{ $log->sign_detected }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:60px;height:6px;background:rgba(255,255,255,0.06);border-radius:2px;overflow:hidden">
                                <div style="width:{{ $log->confidence*100 }}%;height:100%;background:{{ $log->confidence > 0.7 ? '#00ff88' : ($log->confidence > 0.4 ? '#ffa502' : '#ff4757') }};border-radius:2px"></div>
                            </div>
                            <span class="text-xs">{{ round($log->confidence*100,1) }}%</span>
                        </div>
                    </td>
                    <td>{!! $log->is_correct ? '<span class="badge badge-success">✓</span>' : '<span class="badge badge-danger">✕</span>' !!}</td>
                    <td class="text-dim font-mono text-xs">{{ $log->response_time_ms }}ms</td>
                    <td class="text-dim text-xs">{{ $log->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-dim" style="text-align:center;padding:32px">Belum ada log AI</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $logs->links('vendor.pagination.simple') }}</div>
</div>
@endsection

@section('scripts')
<script>
fetch('/admin/api/ai-accuracy')
    .then(r=>r.json())
    .then(data=>{
        new Chart(document.getElementById('aiChart'),{
            type:'line',
            data:{
                labels:data.map(d=>d.date),
                datasets:[{
                    label:'Akurasi %',
                    data:data.map(d=>d.accuracy),
                    borderColor:'#00ff88',
                    backgroundColor:'rgba(0,255,136,0.08)',
                    fill:true,tension:.4,borderWidth:2,
                    pointBackgroundColor:'#00ff88',pointBorderColor:'#0f0f11',pointBorderWidth:2,pointRadius:3
                },{
                    label:'Total Prediksi',
                    data:data.map(d=>d.total),
                    borderColor:'#3b82f6',
                    backgroundColor:'transparent',
                    tension:.4,borderWidth:2,borderDash:[5,5],
                    pointRadius:0,yAxisID:'y1'
                }]
            },
            options:{
                responsive:true,maintainAspectRatio:false,
                plugins:{legend:{labels:{font:{size:11},usePointStyle:true,pointStyle:'rect'}}},
                scales:{
                    x:{grid:{display:false},ticks:{font:{size:10}}},
                    y:{beginAtZero:true,max:100,grid:{color:'rgba(255,255,255,0.03)'},ticks:{callback:v=>v+'%',font:{size:10}}},
                    y1:{position:'right',beginAtZero:true,grid:{display:false},ticks:{font:{size:10}}}
                }
            }
        });
    });
</script>
@endsection
