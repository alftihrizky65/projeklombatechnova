@extends('layouts.admin')
@section('title', 'My Progress')

@section('content')
<!-- Student Profile Stats -->
<div class="grid grid-4 mb-6">
    <div class="card stat-card">
        <span class="stat-icon">⭐</span>
        <div class="stat-label">Level Saya</div>
        <div class="stat-value">Lv.{{ $user->level }}</div>
        <div class="stat-sub">Siswa {{ ucfirst($user->name) }}</div>
    </div>
    <div class="card stat-card">
        <span class="stat-icon">⚡</span>
        <div class="stat-label">Total XP</div>
        <div class="stat-value text-accent">{{ number_format($user->xp) }}</div>
        <div class="stat-sub">{{ $weeklyXp }} XP didapat minggu ini</div>
    </div>
    <div class="card stat-card">
        <span class="stat-icon">🔥</span>
        <div class="stat-label">Streak</div>
        <div class="stat-value" style="color:#ffa502">{{ $user->streak }} Hari</div>
        <div class="stat-sub">Jangan berhenti belajar!</div>
    </div>
    <div class="card stat-card">
        <span class="stat-icon">✅</span>
        <div class="stat-label">Isyarat Dikuasai</div>
        <div class="stat-value">{{ $completedSigns }}</div>
        <div class="stat-sub">Dari seluruh modul</div>
    </div>
</div>

<div class="grid grid-2 mb-6" style="grid-template-columns: 2fr 1fr">
    <!-- Learning Classes -->
    <div class="card">
        <div class="card-title" style="display:flex; align-items:center; gap:10px">
            <span style="background:var(--accent); color:#000; padding:2px 8px; border-radius:3px; font-size:10px">NEW</span>
            📚 Daftar Kelas Anda
        </div>
        
        <div class="flex flex-col gap-4">
            @forelse($classes as $class)
            <div style="background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); padding:20px; border-radius:4px;">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <div style="font-size:10px; font-family:var(--font-heading); color:var(--accent); margin-bottom:4px; text-transform:uppercase">{{ $class->difficulty }}</div>
                        <h4 class="font-heading" style="font-size:18px">{{ $class->title }}</h4>
                        <p class="text-dim text-xs mt-1">{{ $class->description }}</p>
                    </div>
                    @if($class->is_completed)
                        <div style="background:var(--accent); color:#000; padding:4px 10px; border-radius:4px; font-size:10px; font-weight:700; font-family:var(--font-heading)">COMPLETED ✅</div>
                    @endif
                </div>

                <!-- Progress Stats -->
                <div class="grid grid-2 mb-4" style="gap:12px">
                    <div style="background:rgba(255,255,255,0.03); padding:10px; border-radius:4px">
                        <div class="text-dim" style="font-size:9px; text-transform:uppercase; margin-bottom:2px">Teori (Kuis)</div>
                        <div class="font-mono text-sm">{{ $class->passed_quizzes }} / {{ $class->quizzes->count() }} Lulus</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.03); padding:10px; border-radius:4px">
                        <div class="text-dim" style="font-size:9px; text-transform:uppercase; margin-bottom:2px">Praktik (AI)</div>
                        <div class="font-mono text-sm">{{ $class->practical_count }} / {{ $class->required_practical_count }} Selesai</div>
                    </div>
                </div>

                <div class="flex gap-3">
                    @if($class->is_completed)
                        <a href="{{ route('admin.certificate', $class->id) }}" target="_blank" class="btn btn-accent w-full" style="background:linear-gradient(45deg, #00ff88, #3b82f6); color:#000; border:none">
                             AMBIL SERTIFIKAT 🎓
                        </a>
                    @else
                        @if($class->passed_quizzes < $class->quizzes->count())
                            @php 
                                $nextQuiz = $class->quizzes->first(function($q) use ($quizScores) {
                                    return !isset($quizScores[$q->id]) || $quizScores[$q->id] < 70;
                                });
                            @endphp
                            <a href="/admin/quizzes/{{ $nextQuiz->id }}" class="btn btn-accent w-full">Lanjutkan Kuis →</a>
                        @else
                            <a href="http://localhost:3000/dashboard" class="btn w-full" style="background:#ffa502; color:#000; border:none">
                                Mulai Praktik Kamera 📷
                            </a>
                        @endif
                    @endif
                </div>
            </div>
            @empty
            <p class="text-dim">Belum ada kelas tersedia.</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Tutorials -->
    <div class="card">
        <div class="card-title">🎬 Tutorial Terbaru</div>
        <div class="flex flex-col gap-4">
            @foreach($recentTutorials as $tutorial)
            <div class="flex gap-3 items-center">
                <div style="width:60px; height:40px; background:#000; border:1px solid var(--border); border-radius:3px; flex-shrink:0; overflow:hidden">
                    @if($tutorial->thumbnail_url)
                        <img src="{{ $tutorial->thumbnail_url }}" style="width:100%; height:100%; object-fit:cover">
                    @else
                        <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:10px; color:var(--text-dim)">8-BIT</div>
                    @endif
                </div>
                <div style="flex:1">
                    <div class="text-sm font-semibold" style="line-height:1.2">{{ $tutorial->title }}</div>
                    <div class="text-xs text-dim">{{ $tutorial->category }} · {{ ucfirst($tutorial->difficulty) }}</div>
                </div>
            </div>
            @endforeach
        </div>
        <a href="/admin/content/tutorials" class="btn btn-ghost btn-sm w-full mt-4">Lihat Semua Tutorial</a>
    </div>
</div>

<div class="card">
    <div class="card-title">🏆 Papan Peringkat (Global)</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Rank</th><th>Siswa</th><th>Level</th><th>XP</th></tr>
            </thead>
            <tbody>
                @php $topUsers = \App\Models\User::where('role', 'user')->orderByDesc('xp')->take(5)->get(); @endphp
                @foreach($topUsers as $i => $u)
                <tr style="{{ $u->id == $user->id ? 'background:rgba(0,255,136,0.05)' : '' }}">
                    <td class="font-mono" style="color:{{ $i==0?'#ffd700':($i==1?'#c0c0c0':($i==2?'#cd7f32':'var(--text-dim)')) }}">#{{ $i+1 }}</td>
                    <td>
                        <div style="font-weight:600">{{ $u->name }} {{ $u->id == $user->id ? '(Anda)' : '' }}</div>
                    </td>
                    <td><span class="badge badge-accent">Lv.{{ $u->level }}</span></td>
                    <td class="font-mono text-accent">{{ number_format($u->xp) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Milestone Popups
    @if(isset($milestonePopup))
        @if($milestonePopup === 'intermediate')
            Swal.fire({
                title: 'LUAR BIASA! 🌟',
                html: 'Selamat! Anda telah mencapai <b>10.000 XP</b>.<br>Anda sekarang berada di tingkat <b>INTERMEDIATE</b> dalam pembelajaran.',
                icon: 'success',
                confirmButtonText: 'Lanjutkan Belajar',
                confirmButtonColor: '#00ff88',
                backdrop: `rgba(0,255,136,0.1)`
            });
            confetti({ particleCount: 200, spread: 70, origin: { y: 0.6 } });
        @elseif($milestonePopup === 'pro')
            Swal.fire({
                title: 'SELAMAT! ANDA LULUS! 🏆',
                html: 'Pencapaian Fantastis! <b>100.000 XP</b> telah tercapai.<br>Anda sekarang adalah seorang <b>PRO Bahasa Isyarat</b>.',
                icon: 'success',
                confirmButtonText: 'Selesai',
                confirmButtonColor: '#00ff88',
                backdrop: `rgba(0,255,136,0.2)`
            });
            // Mega Confetti
            var duration = 5 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };
            function randomInRange(min, max) { return Math.random() * (max - min) + min; }
            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();
                if (timeLeft <= 0) { return clearInterval(interval); }
                var particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        @endif
    @endif
</script>
@endsection
