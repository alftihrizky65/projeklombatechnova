@extends('layouts.admin')
@section('title', 'Kuis: ' . $quiz->title)

@section('content')
<div class="card" id="quiz-container" style="max-width:800px; margin:0 auto; padding:40px">
    <!-- Progress Bar -->
    <div style="margin-bottom:30px">
        <div class="flex justify-between text-xs mb-2">
            <span class="text-dim">PROGRES KUIS</span>
            <span id="progress-text">1 / {{ $quiz->questions->count() }}</span>
        </div>
        <div style="width:100%; height:8px; background:rgba(255,255,255,0.05); border-radius:4px; overflow:hidden">
            <div id="progress-bar" style="width:0%; height:100%; background:linear-gradient(90deg,#00ff88,#00cc6a); transition:width 0.3s ease"></div>
        </div>
    </div>

    <!-- Questions Wrapper -->
    <div id="questions-wrapper">
        @foreach($quiz->questions as $index => $q)
        <div class="quiz-question" id="q-{{ $index }}" style="display: {{ $index === 0 ? 'block' : 'none' }}">
            <div class="grid grid-2" style="gap:30px">
                <!-- Image Section -->
                <div style="background:#000; border:2px solid var(--border); border-radius:4px; aspect-ratio:4/3; display:flex; align-items:center; justify-content:center; overflow:hidden">
                    @if($q->image_url)
                        <img src="{{ $q->image_url }}" style="width:100%; height:100%; object-fit:cover">
                    @else
                        <span class="text-dim font-mono">NO IMAGE</span>
                    @endif
                </div>

                <!-- Text & Options Section -->
                <div>
                    <h3 class="font-mono text-accent mb-6" style="font-size:18px; line-height:1.4">{{ $q->question_text }}</h3>
                    <div class="flex flex-col gap-3 mb-6">
                        @foreach($q->options as $option)
                        <button class="btn btn-ghost option-btn" style="text-align:left; justify-content:flex-start; padding:15px 20px; transition: all 0.2s" onclick="selectOption(this, '{{ $option }}')">
                            {{ $option }}
                        </button>
                        @endforeach
                    </div>
                    <button class="btn btn-accent w-full" onclick="handleNext({{ $index }}, '{{ $q->correct_answer }}')">
                        {{ $index === $quiz->questions->count() - 1 ? 'Selesai & Kirim' : 'Pertanyaan Selanjutnya →' }}
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Result Screen (Hidden) -->
    <div id="result-screen" style="display:none; text-align:center; padding:20px 0">
        <div id="result-icon" style="font-size:60px; margin-bottom:20px">🏆</div>
        <h2 id="result-title" class="font-heading mb-2">KUIS SELESAI!</h2>
        <p class="text-dim mb-6">Skor Anda: <span id="final-score" class="text-accent" style="font-weight:700">0</span></p>
        
        <div id="reward-box" class="card" style="background:rgba(0,255,136,0.05); border:1px dashed var(--accent); margin-bottom:30px">
            <div class="text-sm">REWARD XP</div>
            <div class="stat-value text-accent" id="xp-reward">+0 XP</div>
        </div>

        <div id="fail-box" class="card" style="display:none; background:rgba(255,71,87,0.05); border:1px dashed #ff4757; margin-bottom:30px">
            <p class="text-sm" style="color:#ff4757">Skor minimal untuk lulus adalah 70. Silakan coba lagi!</p>
        </div>

        <div class="flex gap-4 justify-center">
            <a href="/admin/content/quizzes" id="btn-finish" class="btn btn-accent" style="display:none">Kembali ke Daftar</a>
            <button onclick="location.reload()" class="btn btn-ghost">Ulangi Kuis</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentIdx = 0;
    let totalQuestions = {{ $quiz->questions->count() }};
    let correctAnswers = 0;
    let selectedAnswer = null;

    function selectOption(btn, option) {
        const parent = btn.parentElement;
        parent.querySelectorAll('.option-btn').forEach(b => {
            b.classList.remove('active');
            b.style.background = 'rgba(255,255,255,0.05)';
            b.style.borderColor = 'var(--border)';
        });
        btn.classList.add('active');
        btn.style.background = 'rgba(0,255,136,0.1)';
        btn.style.borderColor = 'var(--accent)';
        selectedAnswer = option;
    }

    function handleNext(index, correct) {
        if (!selectedAnswer) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Jawaban!',
                text: 'Silakan pilih salah satu jawaban sebelum lanjut.',
                confirmButtonColor: '#00ff88'
            });
            return;
        }
        if (selectedAnswer === correct) {
            correctAnswers++;
        }
        selectedAnswer = null;
        document.getElementById(`q-${index}`).style.display = 'none';
        currentIdx++;
        if (currentIdx < totalQuestions) {
            document.getElementById(`q-${currentIdx}`).style.display = 'block';
            updateProgress();
        } else {
            finishQuiz();
        }
    }

    function updateProgress() {
        let percent = (currentIdx / totalQuestions) * 100;
        document.getElementById('progress-bar').style.width = percent + '%';
        document.getElementById('progress-text').innerText = `${currentIdx + 1} / ${totalQuestions}`;
    }

    function finishQuiz() {
        document.getElementById('progress-bar').style.width = '100%';
        let score = Math.round((correctAnswers / totalQuestions) * 100);
        
        Swal.fire({
            title: 'Mengirim Jawaban...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading() }
        });

        fetch(`/admin/quizzes/{{ $quiz->id }}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ score: score })
        })
        .then(response => {
            if (!response.ok) return response.json().then(err => { throw err; });
            return response.json();
        })
        .then(data => {
            Swal.close();
            document.getElementById('questions-wrapper').style.display = 'none';
            document.getElementById('result-screen').style.display = 'block';
            document.getElementById('final-score').innerText = score;

            if (data.passed) {
                let milestoneTitle = '';
                let milestoneText = '';
                
                if (data.milestone === 'intermediate') {
                    milestoneTitle = 'LUAR BIASA! 🌟';
                    milestoneText = '<br><br><b>PENCAPAIAN BARU:</b> Anda telah mencapai 10.000 XP dan menjadi <b>INTERMEDIATE</b>!';
                } else if (data.milestone === 'pro') {
                    milestoneTitle = 'SELAMAT! ANDA LULUS! 🏆';
                    milestoneText = '<br><br><b>PENCAPAIAN TERAKHIR:</b> 100.000 XP tercapai! Anda adalah <b>PRO Bahasa Isyarat</b>!';
                }

                Swal.fire({
                    title: milestoneTitle || 'SELAMAT! 🎉',
                    html: `Skor Anda: <b>${score}</b><br>Anda Lulus! ` + 
                          (data.daily_limit_reached ? '<br><i style="color:#ff4757; font-size:11px">Limit harian tercapai. XP tidak bertambah.</i>' : `Berhasil Mendapatkan <b>${data.xp_gained} XP</b>`) +
                          milestoneText,
                    icon: 'success',
                    confirmButtonText: 'Mantap!',
                    confirmButtonColor: '#00ff88'
                });

                document.getElementById('result-icon').innerText = '🏆';
                document.getElementById('result-title').innerText = 'KUIS LULUS!';
                document.getElementById('reward-box').style.display = 'block';
                document.getElementById('fail-box').style.display = 'none';
                document.getElementById('btn-finish').style.display = 'inline-block';
                document.getElementById('xp-reward').innerText = data.daily_limit_reached ? '+0 XP (Limit)' : `+${data.xp_gained} XP`;
                
                confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 }, colors: ['#00ff88', '#3b82f6', '#ffffff'] });
                if (data.milestone === 'pro') {
                    // Extra confetti for pro
                    setTimeout(() => confetti({ particleCount: 300, spread: 100, origin: { y: 0.5 } }), 500);
                }
            } else {
                Swal.fire({
                    title: 'YAH, GAGAL... ❌',
                    html: `Skor Anda: <b>${score}</b><br>Maaf, Anda belum lulus. Skor minimal adalah 70.`,
                    icon: 'error',
                    confirmButtonText: 'Coba Lagi',
                    confirmButtonColor: '#ff4757'
                });
                document.getElementById('result-icon').innerText = '❌';
                document.getElementById('result-title').innerText = 'KUIS GAGAL';
                document.getElementById('reward-box').style.display = 'none';
                document.getElementById('fail-box').style.display = 'block';
                document.getElementById('btn-finish').style.display = 'none';
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan: ' + (error.message || 'Gagal menyimpan nilai'),
                confirmButtonColor: '#ff4757'
            });
        });
    }
</script>
@endsection
