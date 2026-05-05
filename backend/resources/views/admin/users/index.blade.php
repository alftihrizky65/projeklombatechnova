@extends('layouts.admin')
@section('title', 'Manajemen User')

@section('content')
<div class="flex justify-between items-center mb-6">
    <form method="GET" class="search-bar">
        <input type="text" name="search" class="form-input" placeholder="Cari nama atau email..." value="{{ request('search') }}">
        <select name="role" class="form-input" style="max-width:160px" onchange="this.form.submit()">
            <option value="">Semua Role</option>
            <option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option>
            <option value="content_manager" {{ request('role')=='content_manager'?'selected':'' }}>Content Manager</option>
            <option value="user" {{ request('role')=='user'?'selected':'' }}>User</option>
        </select>
        <button type="submit" class="btn btn-accent btn-sm">Cari</button>
    </form>
    <a href="/admin/users/create" class="btn btn-accent">+ Tambah User</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>User</th><th>Role</th><th>Level</th><th>XP</th><th>Bergabung</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $u->name }}</div>
                        <div class="text-xs text-dim">{{ $u->email }}</div>
                    </td>
                    <td>
                        @if($u->role === 'admin')
                            <span class="badge badge-danger">Admin</span>
                        @elseif($u->role === 'content_manager')
                            <span class="badge badge-warning">Content Mgr</span>
                        @else
                            <span class="badge badge-info">User</span>
                        @endif
                    </td>
                    <td><span class="badge badge-accent">Lv.{{ $u->level }}</span></td>
                    <td class="font-mono text-accent">{{ number_format($u->xp) }}</td>
                    <td class="text-dim text-xs">{{ $u->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex gap-2">
                            <a href="/admin/users/{{ $u->id }}/edit" class="btn btn-ghost btn-sm">Edit</a>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="/admin/users/{{ $u->id }}" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-dim" style="text-align:center;padding:32px">Tidak ada user ditemukan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $users->links('vendor.pagination.simple') }}</div>
</div>
@endsection
