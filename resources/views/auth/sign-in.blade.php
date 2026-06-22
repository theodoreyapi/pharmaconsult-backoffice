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

       /* ─── RIGHT PANEL — REDESIGN ─────────────────────── */
    .auth-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 24px;
        background:
            radial-gradient(circle at 85% 15%, rgba(65, 186, 62, 0.06), transparent 45%),
            var(--off-white);
        position: relative;
    }

    .auth-right::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(65, 186, 62, 0.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(65, 186, 62, 0.035) 1px, transparent 1px);
        background-size: 44px 44px;
        mask-image: radial-gradient(ellipse 70% 60% at 50% 40%, black 0%, transparent 75%);
        pointer-events: none;
    }

    .login-card {
        width: 100%;
        max-width: 420px;
        background: var(--white);
        border-radius: 28px;
        padding: 48px 44px;
        box-shadow:
            0 1px 0 rgba(255, 255, 255, 0.6) inset,
            0 24px 70px rgba(13, 27, 14, 0.10),
            0 2px 6px rgba(13, 27, 14, 0.05);
        border: 1px solid rgba(65, 186, 62, 0.12);
        position: relative;
        animation: cardIn .6s cubic-bezier(.16, 1, .3, 1) both;
    }

    @keyframes cardIn {
        from { opacity: 0; transform: translateY(18px) scale(.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* ── Logo ──────────────────────────────────────── */
    .logo-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 36px;
    }

    .logo-icon {
        width: 46px;
        height: 46px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(145deg, #f3faf2, #e6f5e4);
        border: 1px solid rgba(65, 186, 62, 0.18);
        flex-shrink: 0;
    }

    .logo-icon img {
        width: 26px;
        height: auto;
    }

    .logo-name {
        font-family: 'Sora', sans-serif;
        font-size: 15px;
        font-weight: 600;
        color: var(--text-dark);
        letter-spacing: -0.01em;
        line-height: 1.2;
    }

    .logo-tag {
        font-size: 11px;
        color: var(--text-light);
        letter-spacing: 0.04em;
    }

    /* ── Heading ───────────────────────────────────── */
    .card-eyebrow {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--green-dark);
        margin-bottom: 10px;
        display: block;
    }

    .card-title {
        font-family: 'Sora', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: var(--text-dark);
        letter-spacing: -0.035em;
        line-height: 1.15;
        margin-bottom: 8px;
    }

    .card-sub {
        font-size: 14px;
        color: var(--text-mid);
        margin-bottom: 34px;
        line-height: 1.5;
    }

    /* ── Fields — underline style, more refined ───── */
    .field {
        position: relative;
        margin-bottom: 22px;
    }

    .field-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-mid);
        margin-bottom: 8px;
        letter-spacing: 0.01em;
    }

    .field-input-row {
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1.5px solid #e2eae4;
        padding-bottom: 11px;
        transition: border-color .25s ease;
    }

    .field-input-row:focus-within {
        border-color: var(--green);
    }

    .field-icon {
        font-size: 17px;
        color: var(--text-light);
        flex-shrink: 0;
        transition: color .25s ease;
    }

    .field-input-row:focus-within .field-icon {
        color: var(--green);
    }

    .field input {
        flex: 1;
        border: none;
        background: transparent;
        font-size: 15px;
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
        outline: none;
        padding: 2px 0;
    }

    .field input::placeholder {
        color: #c2ccc4;
    }

    .field-input-row.is-invalid {
        border-color: #e24b4a;
    }

    .eye-toggle {
        cursor: pointer;
        font-size: 17px;
        color: var(--text-light);
        transition: color .2s;
        flex-shrink: 0;
    }

    .eye-toggle:hover {
        color: var(--green);
    }

    /* ── Submit ────────────────────────────────────── */
    .btn-submit {
        width: 100%;
        height: 52px;
        border-radius: 14px;
        border: none;
        background: linear-gradient(135deg, var(--green), var(--green-dark));
        color: white;
        font-family: 'Sora', sans-serif;
        font-size: 14.5px;
        font-weight: 600;
        letter-spacing: -0.005em;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 8px;
        box-shadow: 0 10px 28px rgba(65, 186, 62, 0.30);
        transition: transform .25s cubic-bezier(.16,1,.3,1), box-shadow .25s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 34px rgba(65, 186, 62, 0.38);
    }

    .btn-submit:active {
        transform: translateY(0);
        box-shadow: 0 6px 16px rgba(65, 186, 62, 0.22);
    }

    .btn-submit i {
        font-size: 17px;
    }

    /* ── Trust row ─────────────────────────────────── */
    .trust-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 28px;
    }

    .trust-badge {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 500;
        color: var(--text-light);
        background: var(--off-white);
        border: 1px solid #e6ede8;
        border-radius: 20px;
        padding: 6px 13px;
    }

    .trust-badge i {
        font-size: 13px;
        color: var(--green);
    }

    .card-footer-text {
        text-align: center;
        margin-top: 24px;
        font-size: 11.5px;
        color: #c4ccc6;
        letter-spacing: 0.01em;
    }

    /* ── Focus visibility (accessibility) ─────────── */
    .btn-submit:focus-visible,
    .field input:focus-visible {
        outline: 2px solid var(--green-dark);
        outline-offset: 3px;
    }

    @media (prefers-reduced-motion: reduce) {
        .login-card { animation: none; }
        .btn-submit:hover { transform: none; }
    }

    @media (max-width: 768px) {
        .login-card {
            padding: 38px 30px;
            border-radius: 22px;
        }
    }

    @media (max-width: 400px) {
        .login-card { padding: 30px 22px; }
        .card-title { font-size: 24px; }
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
                <img src="{{ URL::asset('assets/images/couleur.png') }}" alt="PharmaConsults">
            </div>
            <div>
                <div class="logo-name">PharmaConsults</div>
                <div class="logo-tag">Espace administration</div>
            </div>
        </div>

        <span class="card-eyebrow">Accès sécurisé</span>
        <h3 class="card-title">Bon retour</h3>
        <p class="card-sub">Connectez-vous pour piloter vos officines</p>

        @include('layouts.statuts')

        <form action="{{ url('custom-login') }}" method="POST" autocomplete="on">
            @csrf

            {{-- EMAIL --}}
            <div class="field">
                <label class="field-label" for="email-field">Adresse e-mail</label>
                <div class="field-input-row {{ $errors->has('email') ? 'is-invalid' : '' }}">
                    <span class="field-icon ri-mail-line"></span>
                    <input type="email" name="email" id="email-field" required
                        placeholder="vous@pharmacie.com" autocomplete="email"
                        value="{{ old('email') }}">
                </div>
            </div>

            {{-- PASSWORD --}}
            <div class="field">
                <label class="field-label" for="pw-field">Mot de passe</label>
                <div class="field-input-row {{ $errors->has('password') ? 'is-invalid' : '' }}">
                    <span class="field-icon ri-lock-password-line"></span>
                    <input type="password" name="password" id="pw-field" required
                        placeholder="••••••••" autocomplete="current-password">
                    <span class="eye-toggle ri-eye-line" id="eye-btn" data-toggle="#pw-field"></span>
                </div>
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
