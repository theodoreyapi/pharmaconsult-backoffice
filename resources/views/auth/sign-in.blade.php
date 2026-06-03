<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmaconsults Admin — Connexion</title>

    <link rel="icon" type="image/x-icon" href="{{ URL::asset('') }}assets/images/favicon.ico">
    <link rel="stylesheet" href="{{ URL::asset('') }}assets/css/remixicon.css">
    <link rel="stylesheet" href="{{ URL::asset('') }}assets/css/lib/bootstrap.min.css">
    <link rel="stylesheet" href="{{ URL::asset('') }}assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Inter:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --green: #41BA3E;
            --green-dark: #2d8c30;
            --green-deep: #1a4731;
            --green-light: rgba(65, 186, 62, 0.10);
            --white: #ffffff;
            --off-white: #f7f9f7;
            --text-dark: #0d1b0e;
            --text-mid: #4a6050;
            --text-light: #8ca494;
            --border: rgba(65, 186, 62, 0.18);
            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --shadow-card: 0 20px 60px rgba(13, 27, 14, 0.10), 0 2px 8px rgba(13, 27, 14, 0.06);
        }

        html,
        body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--off-white);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* ─── LAYOUT ─────────────────────────────────────── */
        .auth-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ─── LEFT PANEL ─────────────────────────────────── */
        .auth-left {
            width: 46%;
            background:
                linear-gradient(160deg, #0f2e1a 0%, #1c5c2e 40%, #2d8c30 75%, #41BA3E 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 52px;
            position: relative;
            overflow: hidden;
        }

        /* decorative circles */
        .auth-left::before {
            content: '';
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.07);
            top: -160px;
            right: -140px;
        }

        .auth-left::after {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.06);
            bottom: -80px;
            left: -80px;
        }

        .deco-dot {
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
        }

        .deco-dot:nth-child(1) {
            top: 22%;
            left: 8%;
        }

        .deco-dot:nth-child(2) {
            top: 55%;
            right: 12%;
            width: 4px;
            height: 4px;
        }

        .deco-dot:nth-child(3) {
            bottom: 30%;
            left: 18%;
            width: 8px;
            height: 8px;
            opacity: .15;
        }

        .left-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 2;
        }

        .left-brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
        }

        .left-brand-name {
            font-family: 'Sora', sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: white;
            letter-spacing: -0.02em;
        }

        .left-brand-tag {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.55);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-top: 1px;
        }

        .left-hero {
            position: relative;
            z-index: 2;
        }

        .left-hero-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.55);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 5px 14px;
            margin-bottom: 20px;
        }

        .left-hero-label::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #7fffab;
        }

        .left-hero h1 {
            font-family: 'Sora', sans-serif;
            font-size: 36px;
            font-weight: 700;
            color: white;
            line-height: 1.22;
            letter-spacing: -0.03em;
            margin-bottom: 16px;
        }

        .left-hero p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.75;
            max-width: 340px;
        }

        .features {
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: relative;
            z-index: 2;
        }

        .feat {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.10);
            border-radius: var(--radius-sm);
            padding: 13px 16px;
            backdrop-filter: blur(6px);
            transition: background .2s;
        }

        .feat:hover {
            background: rgba(255, 255, 255, 0.11);
        }

        .feat-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            color: rgba(255, 255, 255, 0.9);
            flex-shrink: 0;
        }

        .feat-text {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.80);
            line-height: 1.4;
        }

        .feat-text strong {
            display: block;
            color: white;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 1px;
        }

        /* ─── RIGHT PANEL ────────────────────────────────── */
        .auth-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            background: var(--off-white);
            position: relative;
        }

        /* subtle grid bg */
        .auth-right::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(65, 186, 62, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(65, 186, 62, 0.04) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 44px 40px;
            box-shadow: var(--shadow-card);
            border: 1px solid var(--border);
            position: relative;
            animation: cardIn .5s ease both;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* logo */
        .logo-wrap {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 18px;
            /* background: linear-gradient(135deg, white, grey); */
            font-size: 28px;
            color: white;
            box-shadow: 0 8px 24px rgba(65, 186, 62, 0.30);
        }

        .card-title {
            font-family: 'Sora', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--text-dark);
            text-align: center;
            letter-spacing: -0.03em;
            margin-bottom: 6px;
        }

        .card-sub {
            font-size: 14px;
            color: var(--text-light);
            text-align: center;
            margin-bottom: 30px;
        }

        /* fields */
        .field {
            position: relative;
            margin-bottom: 16px;
        }

        .field-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 19px;
            color: var(--text-light);
            pointer-events: none;
            transition: color .2s;
        }

        .field:focus-within .field-icon {
            color: var(--green);
        }

        .field input {
            width: 100%;
            height: 54px;
            border-radius: var(--radius-sm);
            border: 1.5px solid #e2eae4;
            background: var(--off-white);
            padding: 0 48px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            transition: border-color .2s, background .2s, box-shadow .2s;
            outline: none;
        }

        .field input::placeholder {
            color: #b0bdb4;
        }

        .field input:focus {
            border-color: var(--green);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(65, 186, 62, 0.10);
        }

        .field input.is-invalid {
            border-color: #e24b4a;
        }

        .eye-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 19px;
            color: var(--text-light);
            transition: color .2s;
        }

        .eye-toggle:hover {
            color: var(--green);
        }

        /* options row */
        .opts {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .check-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-mid);
            cursor: pointer;
        }

        .check-label input[type=checkbox] {
            accent-color: var(--green);
            width: 15px;
            height: 15px;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 13px;
            color: var(--green);
            text-decoration: none;
            font-weight: 500;
            transition: opacity .2s;
        }

        .forgot-link:hover {
            opacity: .7;
        }

        /* submit */
        .btn-submit {
            width: 100%;
            height: 54px;
            border-radius: var(--radius-sm);
            border: none;
            background: linear-gradient(135deg, var(--green), var(--green-dark));
            color: white;
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: -0.01em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            box-shadow: 0 8px 24px rgba(65, 186, 62, 0.28);
            transition: transform .2s, box-shadow .2s, opacity .2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(65, 186, 62, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(65, 186, 62, 0.20);
        }

        /* trust badges */
        .trust-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 22px;
        }

        .trust-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            color: var(--text-light);
            background: var(--off-white);
            border: 1px solid #e2eae4;
            border-radius: 20px;
            padding: 5px 12px;
        }

        .trust-badge i {
            font-size: 13px;
            color: var(--green);
        }

        .card-footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #c4ccc6;
        }

        /* ─── RESPONSIVE ─────────────────────────────────── */
        @media (max-width: 1024px) {
            .auth-left {
                width: 42%;
                padding: 40px 36px;
            }

            .left-hero h1 {
                font-size: 30px;
            }
        }

        @media (max-width: 768px) {
            .auth-left {
                display: none;
            }

            .auth-right {
                padding: 24px 16px;
            }

            .login-card {
                padding: 36px 28px;
                border-radius: var(--radius-md);
            }
        }

        @media (max-width: 400px) {
            .login-card {
                padding: 28px 20px;
            }

            .card-title {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">

        {{-- ─── LEFT ──────────────────────────────────────────── --}}
        <div class="auth-left d-lg-flex d-none flex-column justify-content-between">
            <span class="deco-dot"></span>
            <span class="deco-dot"></span>
            <span class="deco-dot"></span>

            <div class="left-brand">
                <div class="left-brand-icon">
                    <img src="{{ URL::asset('assets/images/blanc.png') }}" alt="">
                </div>
                <div>
                    <div class="left-brand-name">PharmaConsults</div>
                    <div class="left-brand-tag">Administration</div>
                </div>
            </div>

            <div class="left-hero">
                <div class="left-hero-label">Plateforme Admin</div>
                <h1>Gérez vos officines avec précision.</h1>
                <p>Pilotez vos stocks, vaccinations et données patients depuis un seul espace sécurisé.</p>
            </div>

            <div class="features">
                <div class="feat">
                    <div class="feat-icon"><i class="ri-syringe-line"></i></div>
                    <div class="feat-text">
                        <strong>Vaccination & rendez-vous</strong>
                        Campagnes organisées, suivi patients en temps réel
                    </div>
                </div>
                <div class="feat">
                    <div class="feat-icon"><i class="ri-medicine-bottle-line"></i></div>
                    <div class="feat-text">
                        <strong>Médicaments & stocks</strong>
                        Base intelligente, alertes rupture automatiques
                    </div>
                </div>
                <div class="feat">
                    <div class="feat-icon"><i class="ri-bar-chart-2-line"></i></div>
                    <div class="feat-text">
                        <strong>Tableaux de bord</strong>
                        Vue globale sur toutes vos pharmacies
                    </div>
                </div>
                <div class="feat">
                    <div class="feat-icon"><i class="ri-shield-check-line"></i></div>
                    <div class="feat-text">
                        <strong>Données sécurisées</strong>
                        Chiffrement SSL, accès contrôlé par rôle
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── RIGHT ─────────────────────────────────────────── --}}
        <div class="auth-right">
            <div class="login-card">

                <div class="logo-wrap">
                    <div class="logo-icon">
                        <img src="{{ URL::asset('assets/images/couleur.png') }}" style="width: 60px; height: 70px;" alt="">
                    </div>
                </div>

                <h3 class="card-title">Bon retour 👋</h3>
                <p class="card-sub">Connectez-vous à votre espace administration</p>

                @include('layouts.statuts')

                <form action="{{ url('custom-login') }}" method="POST" autocomplete="on">
                    @csrf

                    {{-- EMAIL --}}
                    <div class="field">
                        <span class="field-icon ri-mail-line"></span>
                        <input type="email" name="email" required placeholder="Adresse e-mail" autocomplete="email"
                            value="{{ old('email') }}" class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
                    </div>

                    {{-- PASSWORD --}}
                    <div class="field">
                        <span class="field-icon ri-lock-password-line"></span>
                        <input type="password" name="password" id="pw-field" required placeholder="Mot de passe"
                            autocomplete="current-password" class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
                        <span class="eye-toggle ri-eye-line" id="eye-btn" data-toggle="#pw-field"></span>
                    </div>

                    {{-- OPTIONS --}}
                    <div class="opts">
                        {{-- <label class="check-label">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            Se souvenir de moi
                        </label> --}}
                        {{-- <a href="{{ url('forgot') }}" class="forgot-link">Mot de passe oublié ?</a> --}}
                    </div>

                    {{-- SUBMIT --}}
                    <button type="submit" class="btn-submit">
                        <i class="ri-login-circle-line"></i>
                        Se connecter
                    </button>

                    {{-- TRUST --}}
                    <div class="trust-row">
                        <div class="trust-badge"><i class="ri-shield-keyhole-line"></i> SSL chiffré</div>
                        <div class="trust-badge"><i class="ri-lock-2-line"></i> Données protégées</div>
                    </div>

                    <p class="card-footer-text">© {{ date('Y') }} Pharmaconsults</p>
                </form>

            </div>
        </div>

    </div>

    <script src="{{ URL::asset('') }}assets/js/lib/jquery-3.7.1.min.js"></script>
    <script src="{{ URL::asset('') }}assets/js/lib/bootstrap.bundle.min.js"></script>
    <script src="{{ URL::asset('') }}assets/js/lib/iconify-icon.min.js"></script>

    <script>
        // Password toggle
        document.getElementById('eye-btn').addEventListener('click', function() {
            var input = document.getElementById('pw-field');
            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.classList.toggle('ri-eye-line', !isPassword);
            this.classList.toggle('ri-eye-off-line', isPassword);
        });
    </script>
</body>

</html>
