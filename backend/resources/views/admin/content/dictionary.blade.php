@extends('layouts.admin')
@section('title', 'Kamus Isyarat')

@section('content')
<div class="flex justify-between items-center mb-6">
    <form method="GET" class="search-bar">
        <input type="text" name="search" class="form-input" placeholder="Cari kata..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-accent btn-sm">Cari</button>
    </form>
    <button onclick="document.getElementById('addModal').classList.add('active')" class="btn btn-accent">+ Tambah Kata</button>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kata</th><th>Kategori</th><th>Deskripsi</th><th>Video</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($entries as $e)
                <tr>
                    <td style="font-weight:600;font-family:var(--font-heading)">{{ $e->word }}</td>
                    <td><span class="badge badge-info">{{ $e->category }}</span></td>
                    <td class="text-dim text-sm">{{ Str::limit($e->description, 40) }}</td>
                    <td>{!! $e->video_url ? '<a href="'.$e->video_url.'" target="_blank" class="text-accent text-sm">▶ Lihat</a>' : '<span class="text-dim">—</span>' !!}</td>
                    <td>
                        <div class="flex gap-2">
                            <button onclick="editEntry({{ $e->id }},'{{ addslashes($e->word) }}','{{ $e->video_url }}','{{ $e->thumbnail_url }}','{{ $e->category }}','{{ addslashes($e->description) }}')" class="btn btn-ghost btn-sm">Edit</button>
                            <form method="POST" action="/admin/content/dictionary/{{ $e->id }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-dim" style="text-align:center;padding:32px">Belum ada data kamus</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $entries->links('vendor.pagination.simple') }}</div>
</div>

<div class="modal-backdrop" id="addModal">
    <div class="modal">
        <div class="modal-title">Tambah Kata Baru</div>
        <form method="POST" action="/admin/content/dictionary">
            @csrf
            <div class="form-group"><label class="form-label">Kata</label><input type="text" name="word" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Video URL</label><input type="url" name="video_url" class="form-input"></div>
            <div class="form-group"><label class="form-label">Thumbnail URL</label><input type="url" name="thumbnail_url" class="form-input"></div>
            <div class="form-group"><label class="form-label">Kategori</label><input type="text" name="category" class="form-input" value="umum" required></div>
            <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" class="form-input"></textarea></div>
            <div class="flex gap-3"><button type="submit" class="btn btn-accent">Simpan</button><button type="button" onclick="document.getElementById('addModal').classList.remove('active')" class="btn">Batal</button></div>
        </form>
    </div>
</div>

<div class="modal-backdrop" id="editModal">
    <div class="modal">
        <div class="modal-title">Edit Kata</div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="form-group"><label class="form-label">Kata</label><input type="text" name="word" id="e_word" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Video URL</label><input type="url" name="video_url" id="e_video" class="form-input"></div>
            <div class="form-group"><label class="form-label">Thumbnail URL</label><input type="url" name="thumbnail_url" id="e_thumb" class="form-input"></div>
            <div class="form-group"><label class="form-label">Kategori</label><input type="text" name="category" id="e_cat" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" id="e_desc" class="form-input"></textarea></div>
            <div class="flex gap-3"><button type="submit" class="btn btn-accent">Update</button><button type="button" onclick="document.getElementById('editModal').classList.remove('active')" class="btn">Batal</button></div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editEntry(id,word,video,thumb,cat,desc){
    document.getElementById('editForm').action='/admin/content/dictionary/'+id;
    document.getElementById('e_word').value=word;
    document.getElementById('e_video').value=video||'';
    document.getElementById('e_thumb').value=thumb||'';
    document.getElementById('e_cat').value=cat;
    document.getElementById('e_desc').value=desc;
    document.getElementById('editModal').classList.add('active');
}
</script>
@endsection
