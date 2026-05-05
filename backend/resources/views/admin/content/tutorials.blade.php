@extends('layouts.admin')
@section('title', 'Video Tutorial')

@section('content')
<div class="flex justify-between items-center mb-6">
    <form method="GET" class="search-bar">
        <input type="text" name="search" class="form-input" placeholder="Cari tutorial..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-accent btn-sm">Cari</button>
    </form>
    @if(auth()->user()->role !== 'user')
    <button onclick="document.getElementById('addModal').classList.add('active')" class="btn btn-accent">+ Tambah Tutorial</button>
    @endif
</div>

@if(auth()->user()->role === 'user')
<!-- User View: Tutorial Cards -->
<div class="grid grid-3">
    @forelse($tutorials as $t)
    <div class="card" style="padding:0; overflow:hidden">
        <div style="aspect-ratio:16/9; background:#000; position:relative">
            @if($t->thumbnail_url)
                <img src="{{ $t->thumbnail_url }}" style="width:100%; height:100%; object-fit:cover; opacity:0.7">
            @endif
            <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center">
                <span style="font-size:40px; opacity:0.8">▶</span>
            </div>
            <div style="position:absolute; top:12px; left:12px">
                <span class="badge badge-info">{{ $t->category }}</span>
            </div>
        </div>
        <div style="padding:20px">
            <h4 class="font-mono text-accent mb-2" style="font-size:16px">{{ $t->title }}</h4>
            <div class="flex justify-between items-center">
                <span class="badge badge-{{ $t->difficulty=='beginner'?'accent':($t->difficulty=='intermediate'?'warning':'danger') }}">{{ ucfirst($t->difficulty) }}</span>
                <a href="#" class="btn btn-ghost btn-sm">Tonton</a>
            </div>
        </div>
    </div>
    @empty
    <p class="text-dim">Belum ada tutorial.</p>
    @endforelse
</div>
@else
<!-- Admin View -->
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Judul</th><th>Kategori</th><th>Difficulty</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($tutorials as $t)
                <tr>
                    <td style="font-weight:600">{{ $t->title }}</td>
                    <td><span class="badge badge-info">{{ $t->category }}</span></td>
                    <td><span class="badge badge-{{ $t->difficulty=='beginner'?'accent':($t->difficulty=='intermediate'?'warning':'danger') }}">{{ ucfirst($t->difficulty) }}</span></td>
                    <td>{!! $t->is_published ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-warning">Draft</span>' !!}</td>
                    <td class="text-dim text-xs">{{ $t->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex gap-2">
                            <button onclick="editTutorial({{ $t->id }}, '{{ addslashes($t->title) }}', '{{ $t->video_url }}', '{{ $t->thumbnail_url }}', '{{ addslashes($t->description) }}', '{{ $t->category }}', '{{ $t->difficulty }}', {{ $t->is_published?'true':'false' }})" class="btn btn-ghost btn-sm">Edit</button>
                            <form method="POST" action="/admin/content/tutorials/{{ $t->id }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-dim" style="text-align:center;padding:32px">Belum ada tutorial</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
<div class="pagination">{{ $tutorials->links('vendor.pagination.simple') }}</div>

@if(auth()->user()->role !== 'user')
<div class="modal-backdrop" id="addModal">
    <div class="modal">
        <div class="modal-title">Tambah Tutorial</div>
        <form method="POST" action="/admin/content/tutorials">
            @csrf
            <div class="form-group"><label class="form-label">Judul</label><input type="text" name="title" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Video URL</label><input type="url" name="video_url" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Thumbnail URL</label><input type="url" name="thumbnail_url" class="form-input"></div>
            <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" class="form-input"></textarea></div>
            <div class="form-group"><label class="form-label">Kategori</label><input type="text" name="category" class="form-input" value="dasar" required></div>
            <div class="form-group"><label class="form-label">Difficulty</label><select name="difficulty" class="form-input"><option value="beginner">Beginner</option><option value="intermediate">Intermediate</option><option value="advanced">Advanced</option></select></div>
            <div class="form-group"><label class="form-label" style="display:inline"><input type="checkbox" name="is_published" value="1"> Publish</label></div>
            <div class="flex gap-3"><button type="submit" class="btn btn-accent">Simpan</button><button type="button" onclick="document.getElementById('addModal').classList.remove('active')" class="btn">Batal</button></div>
        </form>
    </div>
</div>

<div class="modal-backdrop" id="editModal">
    <div class="modal">
        <div class="modal-title">Edit Tutorial</div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="form-group"><label class="form-label">Judul</label><input type="text" name="title" id="e_title" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Video URL</label><input type="url" name="video_url" id="e_video" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Thumbnail URL</label><input type="url" name="thumbnail_url" id="e_thumb" class="form-input"></div>
            <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" id="e_desc" class="form-input"></textarea></div>
            <div class="form-group"><label class="form-label">Kategori</label><input type="text" name="category" id="e_cat" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Difficulty</label><select name="difficulty" id="e_diff" class="form-input"><option value="beginner">Beginner</option><option value="intermediate">Intermediate</option><option value="advanced">Advanced</option></select></div>
            <div class="form-group"><label class="form-label" style="display:inline"><input type="checkbox" name="is_published" id="e_pub" value="1"> Publish</label></div>
            <div class="flex gap-3"><button type="submit" class="btn btn-accent">Update</button><button type="button" onclick="document.getElementById('editModal').classList.remove('active')" class="btn">Batal</button></div>
        </form>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
function editTutorial(id,title,video,thumb,desc,cat,diff,pub){
    document.getElementById('editForm').action='/admin/content/tutorials/'+id;
    document.getElementById('e_title').value=title;
    document.getElementById('e_video').value=video;
    document.getElementById('e_thumb').value=thumb||'';
    document.getElementById('e_desc').value=desc;
    document.getElementById('e_cat').value=cat;
    document.getElementById('e_diff').value=diff;
    document.getElementById('e_pub').checked=pub;
    document.getElementById('editModal').classList.add('active');
}
</script>
@endsection
