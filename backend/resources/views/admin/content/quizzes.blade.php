@extends('layouts.admin')
@section('title', 'Kuis')

@section('content')
<div class="flex justify-between items-center mb-6">
    <form method="GET" class="search-bar">
        <input type="text" name="search" class="form-input" placeholder="Cari kuis..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-accent btn-sm">Cari</button>
    </form>
    @if(auth()->user()->role !== 'user')
    <button onclick="document.getElementById('addModal').classList.add('active')" class="btn btn-accent">+ Tambah Kuis</button>
    @endif
</div>

@if(auth()->user()->role === 'user')
<!-- User View: Quiz Cards -->
<div class="grid grid-3">
    @forelse($quizzes as $q)
    <div class="card" style="position:relative">
        <div style="position:absolute; top:12px; right:12px">
            <span class="badge badge-{{ $q->difficulty=='beginner'?'accent':($q->difficulty=='intermediate'?'warning':'danger') }}">{{ ucfirst($q->difficulty) }}</span>
        </div>
        <div class="font-mono text-accent mb-2" style="font-size:16px; font-weight:700">{{ $q->title }}</div>
        <p class="text-dim text-sm mb-4">{{ $q->description }}</p>
        <div class="flex justify-between items-center mt-auto">
            <span class="text-xs text-dim">Kategori: {{ $q->category }}</span>
            <a href="/admin/quizzes/{{ $q->id }}" class="btn btn-accent btn-sm">Mulai Kuis</a>
        </div>
    </div>
    @empty
    <p class="text-dim">Belum ada kuis tersedia.</p>
    @endforelse
</div>
@else
<!-- Admin View: Management Table -->
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Judul</th><th>Kategori</th><th>Difficulty</th><th>Soal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($quizzes as $q)
                <tr>
                    <td style="font-weight:600">{{ $q->title }}</td>
                    <td><span class="badge badge-info">{{ $q->category }}</span></td>
                    <td><span class="badge badge-{{ $q->difficulty=='beginner'?'accent':($q->difficulty=='intermediate'?'warning':'danger') }}">{{ ucfirst($q->difficulty) }}</span></td>
                    <td class="font-mono text-accent">{{ $q->questions_count }}</td>
                    <td>{!! $q->is_published ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-warning">Draft</span>' !!}</td>
                    <td>
                        <div class="flex gap-2">
                            <a href="/admin/content/quizzes/{{ $q->id }}/questions" class="btn btn-accent btn-sm">Kelola Soal</a>
                            <button onclick="editQuiz({{ $q->id }},'{{ addslashes($q->title) }}','{{ addslashes($q->description) }}','{{ $q->difficulty }}','{{ $q->category }}',{{ $q->is_published?'true':'false' }})" class="btn btn-ghost btn-sm">Edit</button>
                            <form method="POST" action="/admin/content/quizzes/{{ $q->id }}" onsubmit="return confirm('Hapus kuis ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-dim" style="text-align:center;padding:32px">Belum ada kuis</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
<div class="pagination">{{ $quizzes->links('vendor.pagination.simple') }}</div>

@if(auth()->user()->role !== 'user')
<!-- Modals only for Admin/Content Manager -->
<div class="modal-backdrop" id="addModal">
... (rest of modals) ...
@endif
    <div class="modal">
        <div class="modal-title">Tambah Kuis</div>
        <form method="POST" action="/admin/content/quizzes">
            @csrf
            <div class="form-group"><label class="form-label">Judul</label><input type="text" name="title" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" class="form-input"></textarea></div>
            <div class="form-group"><label class="form-label">Kategori</label><input type="text" name="category" class="form-input" value="umum" required></div>
            <div class="form-group"><label class="form-label">Difficulty</label><select name="difficulty" class="form-input"><option value="beginner">Beginner</option><option value="intermediate">Intermediate</option><option value="advanced">Advanced</option></select></div>
            <div class="form-group"><label class="form-label" style="display:inline"><input type="checkbox" name="is_published" value="1"> Publish</label></div>
            <div class="flex gap-3"><button type="submit" class="btn btn-accent">Simpan</button><button type="button" onclick="document.getElementById('addModal').classList.remove('active')" class="btn">Batal</button></div>
        </form>
    </div>
</div>

<div class="modal-backdrop" id="editModal">
    <div class="modal">
        <div class="modal-title">Edit Kuis</div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="form-group"><label class="form-label">Judul</label><input type="text" name="title" id="e_title" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" id="e_desc" class="form-input"></textarea></div>
            <div class="form-group"><label class="form-label">Kategori</label><input type="text" name="category" id="e_cat" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Difficulty</label><select name="difficulty" id="e_diff" class="form-input"><option value="beginner">Beginner</option><option value="intermediate">Intermediate</option><option value="advanced">Advanced</option></select></div>
            <div class="form-group"><label class="form-label" style="display:inline"><input type="checkbox" name="is_published" id="e_pub" value="1"> Publish</label></div>
            <div class="flex gap-3"><button type="submit" class="btn btn-accent">Update</button><button type="button" onclick="document.getElementById('editModal').classList.remove('active')" class="btn">Batal</button></div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editQuiz(id,title,desc,diff,cat,pub){
    document.getElementById('editForm').action='/admin/content/quizzes/'+id;
    document.getElementById('e_title').value=title;
    document.getElementById('e_desc').value=desc;
    document.getElementById('e_diff').value=diff;
    document.getElementById('e_cat').value=cat;
    document.getElementById('e_pub').checked=pub;
    document.getElementById('editModal').classList.add('active');
}
</script>
@endsection
