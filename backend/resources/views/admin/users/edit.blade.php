@extends('layouts.admin')
@section('title', 'Edit User')

@section('content')
<div class="card" style="max-width:560px">
    <div class="card-title">Edit User: {{ $user->name }}</div>
    <form method="POST" action="/admin/users/{{ $user->id }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
            @error('name')<div class="text-danger text-xs mt-4">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
            @error('email')<div class="text-danger text-xs mt-4">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
            <input type="password" name="password" class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">Role</label>
            <select name="role" class="form-input">
                <option value="user" {{ $user->role=='user'?'selected':'' }}>User</option>
                <option value="content_manager" {{ $user->role=='content_manager'?'selected':'' }}>Content Manager</option>
                <option value="admin" {{ $user->role=='admin'?'selected':'' }}>Admin</option>
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn btn-accent">Update</button>
            <a href="/admin/users" class="btn">Batal</a>
        </div>
    </form>
</div>
@endsection
