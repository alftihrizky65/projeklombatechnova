@extends('layouts.admin')
@section('title', 'Kelola Soal: ' . $quiz->title)

@section('content')
<div class="mb-6">
    <a href="/admin/content/quizzes" class="btn btn-ghost btn-sm">← Kembali ke Kuis</a>
</div>

<div class="grid grid-2" style="grid-template-columns: 1fr 2fr; gap:24px">
    <!-- Form Tambah Soal -->
    <div class="card">
        <div class="card-title">Tambah Soal Baru</div>
        <form method="POST" action="/admin/content/quizzes/{{ $quiz->id }}/questions" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Teks Pertanyaan</label>
                <textarea name="question_text" class="form-input" placeholder="Contoh: Apa arti isyarat ini?" required></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Foto Soal (Opsional)</label>
                <input type="file" name="image" class="form-input" accept="image/*">
                <small class="text-dim">Format: JPG, PNG (Max 2MB)</small>
            </div>

            <div class="form-group">
                <label class="form-label">Jawaban Benar</label>
                <input type="text" name="correct_answer" class="form-input" required placeholder="Jawaban yang tepat">
            </div>

            <div class="form-group">
                <label class="form-label">Pilihan Jawaban (Minimal 2)</label>
                <div id="options-container">
                    <input type="text" name="options[]" class="form-input mb-2" placeholder="Opsi 1" required>
                    <input type="text" name="options[]" class="form-input mb-2" placeholder="Opsi 2" required>
                </div>
                <button type="button" onclick="addOption()" class="text-accent text-xs" style="background:none; border:none; cursor:pointer">+ Tambah Opsi</button>
            </div>

            <button type="submit" class="btn btn-accent w-full">Simpan Soal</button>
        </form>
    </div>

    <!-- Daftar Soal -->
    <div class="card">
        <div class="card-title">Daftar Soal ({{ $questions->count() }})</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Preview</th><th>Pertanyaan</th><th>Jawaban</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($questions as $q)
                    <tr>
                        <td>
                            @if($q->image_url)
                                <img src="{{ $q->image_url }}" style="width:50px; height:40px; object-fit:cover; border-radius:3px">
                            @else
                                <span class="text-dim text-xs">No Img</span>
                            @endif
                        </td>
                        <td class="text-sm">{{ Str::limit($q->question_text, 40) }}</td>
                        <td class="text-accent text-sm">{{ $q->correct_answer }}</td>
                        <td>
                            <form method="POST" action="/admin/content/questions/{{ $q->id }}" onsubmit="return confirm('Hapus soal ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-dim" style="text-align:center; padding:32px">Belum ada soal</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function addOption() {
        const container = document.getElementById('options-container');
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'options[]';
        input.className = 'form-input mb-2';
        input.placeholder = 'Opsi ' + (container.children.length + 1);
        container.appendChild(input);
    }
</script>
@endsection
