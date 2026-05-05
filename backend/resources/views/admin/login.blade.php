<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SignEdu Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #0f0f11;
            color: #e0e0e0;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .grid-bg {
            position: fixed; inset: 0; pointer-events: none;
            background-image:
                linear-gradient(rgba(0,255,136,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,255,136,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .glow-orb {
            position: fixed;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(0,255,136,0.08) 0%, transparent 70%);
            border-radius: 50%;
            top: 20%; left: 50%;
            transform: translate(-50%, -50%);
            animation: pulse 4s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.6; }
            50% { transform: translate(-50%, -50%) scale(1.2); opacity: 1; }
        }
        .login-box {
            position: relative; z-index: 10;
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 8px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
        }
        .login-brand {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-brand h1 {
            font-family: 'Space Mono', monospace;
            font-size: 24px;
            color: #00ff88;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .login-brand p {
            font-size: 13px;
            color: #72727e;
            margin-top: 8px;
        }
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-family: 'Space Mono', monospace;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #72727e;
            margin-bottom: 8px;
        }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 4px;
            color: #e0e0e0;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color .2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 0 3px rgba(0,255,136,0.12);
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #00ff88;
            color: #0f0f11;
            border: none;
            border-radius: 4px;
            font-family: 'Space Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: background .2s;
            margin-top: 8px;
        }
        .btn-login:hover { background: #00cc6a; }
        .error-msg {
            background: rgba(255,71,87,0.1);
            border: 1px solid rgba(255,71,87,0.2);
            color: #ff4757;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .pixel-corner {
            position: absolute;
            width: 12px; height: 12px;
            border: 2px solid rgba(0,255,136,0.2);
        }
        .pixel-corner.tl { top: -1px; left: -1px; border-right: none; border-bottom: none; }
        .pixel-corner.tr { top: -1px; right: -1px; border-left: none; border-bottom: none; }
        .pixel-corner.bl { bottom: -1px; left: -1px; border-right: none; border-top: none; }
        .pixel-corner.br { bottom: -1px; right: -1px; border-left: none; border-top: none; }
    </style>
</head>
<body>
    <div class="grid-bg"></div>
    <div class="glow-orb"></div>

    <div class="login-box">
        <div class="pixel-corner tl"></div>
        <div class="pixel-corner tr"></div>
        <div class="pixel-corner bl"></div>
        <div class="pixel-corner br"></div>

        <div class="login-brand">
            <h1>⬛ SignEdu</h1>
            <p>Admin Dashboard</p>
        </div>

        @if($errors->any())
            <div class="error-msg">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/admin/login">
            @csrf
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="admin@signedu.com" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Masuk Dashboard</button>
        </form>
    </div>
</body>
</html>
