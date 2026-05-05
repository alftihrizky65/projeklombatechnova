@extends('layouts.admin')
@section('title', 'Tambah User')

@section('content')
<div class="card" style="max-width:560px">
    <div class="card-title">Tambah User Baru</div>
    <form method="POST" action="/admin/users">
        @csrf
        <div class="form-group">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
            @error('name')<div class="text-danger text-xs mt-4">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" value="{{ old('email') }}" required>
            @error('email')<div class="text-danger text-xs mt-4">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-input" required>
            @error('password')<div class="text-danger text-xs mt-4">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Role</label>
            <select name="role" class="form-input">
                <option value="user">User</option>
                <option value="content_manager">Content Manager</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn btn-accent">Simpan</button>
            <a href="/admin/users" class="btn">Batal</a>
        </div>
    </form>
</div>
@endsection
