@extends('layouts.admin')
@section('title', 'System Logs')

@section('content')
<div class="grid grid-4 mb-6">
    <div class="card stat-card">
        <div class="stat-label">Info</div>
        <div class="stat-value" style="color:#3b82f6">{{ $stats['info'] }}</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Warning</div>
        <div class="stat-value" style="color:#ffa502">{{ $stats['warning'] }}</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Error</div>
        <div class="stat-value" style="color:#ff4757">{{ $stats['error'] }}</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Critical</div>
        <div class="stat-value" style="color:#ff6b81">{{ $stats['critical'] }}</div>
    </div>
</div>

<div class="flex justify-between items-center mb-4">
    <form method="GET" class="search-bar" style="margin-bottom:0">
        <select name="level" class="form-input" style="max-width:150px" onchange="this.form.submit()">
            <option value="">Semua Level</option>
            <option value="info" {{ request('level')=='info'?'selected':'' }}>Info</option>
            <option value="warning" {{ request('level')=='warning'?'selected':'' }}>Warning</option>
            <option value="error" {{ request('level')=='error'?'selected':'' }}>Error</option>
            <option value="critical" {{ request('level')=='critical'?'selected':'' }}>Critical</option>
        </select>
        <input type="text" name="source" class="form-input" placeholder="Filter source..." value="{{ request('source') }}" style="max-width:200px">
        <button type="submit" class="btn btn-accent btn-sm">Filter</button>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Level</th><th>Source</th><th>Pesan</th><th>Waktu</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        @if($log->level === 'critical')
                            <span class="badge badge-danger">CRITICAL</span>
                        @elseif($log->level === 'error')
                            <span class="badge badge-danger">ERROR</span>
                        @elseif($log->level === 'warning')
                            <span class="badge badge-warning">WARNING</span>
                        @else
                            <span class="badge badge-info">INFO</span>
                        @endif
                    </td>
                    <td class="font-mono text-xs">{{ $log->source }}</td>
                    <td class="text-sm">{{ $log->message }}</td>
                    <td class="text-dim text-xs" style="white-space:nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-dim" style="text-align:center;padding:32px">Tidak ada log</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $logs->links('vendor.pagination.simple') }}</div>
</div>
@endsection
