<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kelulusan - {{ $class->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Great+Vibes&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #c5a059;
            --dark-gold: #8e6d2f;
            --border: 20px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px;
        }
        .certificate-container {
            width: 1000px;
            height: 700px;
            background: white;
            padding: 40px;
            position: relative;
            border: 2px solid var(--gold);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }
        .inner-border {
            border: 1px solid var(--dark-gold);
            height: 100%;
            width: 100%;
            padding: 40px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .seal {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 100px;
            opacity: 0.8;
        }
        .header {
            font-family: 'Cinzel', serif;
            color: var(--dark-gold);
            font-size: 48px;
            margin-bottom: 10px;
            letter-spacing: 5px;
        }
        .sub-header {
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 4px;
            font-size: 14px;
            color: #666;
            margin-bottom: 40px;
        }
        .awarded-to {
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        .student-name {
            font-family: 'Great Vibes', cursive;
            font-size: 64px;
            color: #222;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            min-width: 400px;
        }
        .description {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            max-width: 700px;
            margin-bottom: 60px;
        }
        .signatures {
            display: flex;
            justify-content: space-around;
            width: 100%;
            margin-top: auto;
        }
        .sig-box {
            text-align: center;
            width: 200px;
        }
        .sig-line {
            border-top: 1px solid #999;
            margin-top: 10px;
            padding-top: 5px;
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #333;
        }
        .sig-name {
            font-family: 'Great Vibes', cursive;
            font-size: 28px;
            color: #111;
            margin-bottom: -5px;
        }
        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--gold);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; background: white; }
            .certificate-container { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="inner-border">
            <img src="https://api.iconify.design/ri:verified-badge-fill.svg?color=%23c5a059" class="seal">
            
            <div class="header">SERTIFIKAT</div>
            <div class="sub-header">PENCAPAIAN LUAR BIASA</div>

            <div class="awarded-to">Diberikan Kepada:</div>
            <div class="student-name">{{ $user->name }}</div>

            <div class="description">
                Atas dedikasi dan keberhasilannya menyelesaikan seluruh modul dalam <br>
                <strong>{{ $class->title }}</strong> <br>
                yang meliputi Teori Isyarat BISINDO dan Praktik Computer Vision. <br>
                Dinyatakan lulus pada tanggal {{ date('d F Y') }}.
            </div>

            <div class="signatures">
                <div class="sig-box">
                    <div class="sig-name">M. Rizky Gunawan</div>
                    <div class="sig-line">Founder & CEO</div>
                </div>
                <div class="sig-box">
                    <div class="sig-name">Rayhan Fatin F.</div>
                    <div class="sig-line">Lead AI Developer</div>
                </div>
                <div class="sig-box">
                    <div class="sig-name">M. Ramdhan Ashari</div>
                    <div class="sig-line">Project Manager</div>
                </div>
            </div>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">Unduh Sertifikat (PDF)</button>
</body>
</html>
