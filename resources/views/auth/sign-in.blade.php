<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmaconsults Admin - Authentification</title>

    <link rel="icon" type="image/x-icon" href="{{ URL::asset('') }}assets/images/favicon.ico">

    {{-- Icons --}}
    <link rel="stylesheet" href="{{ URL::asset('') }}assets/css/remixicon.css">

    {{-- Bootstrap --}}
    <link rel="stylesheet" href="{{ URL::asset('') }}assets/css/lib/bootstrap.min.css">

    {{-- Main CSS --}}
    <link rel="stylesheet" href="{{ URL::asset('') }}assets/css/style.css">

    <style>
        body {
            overflow-x: hidden;
            background: #f4f7fb;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            position: relative;
        }

        /* LEFT SIDE */
        .auth-left {
            width: 50%;
            background:
                /* linear-gradient(135deg,
                    rgba(15, 23, 42, .92),
                    #41BA3E 92%), */
                url('{{ URL::asset('') }}assets/images/auth/auth-img.png');
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        .auth-left::before {
            content: "";
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
            top: -120px;
            right: -120px;
        }

        .auth-left::after {
            content: "";
            position: absolute;
            width: 350px;
            height: 350px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            bottom: -100px;
            left: -100px;
        }

        .auth-overlay-content {
            position: relative;
            z-index: 2;
            color: white;
            padding: 60px;
            max-width: 650px;
        }

        .auth-overlay-content h1 {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .auth-overlay-content p {
            font-size: 17px;
            opacity: .88;
            line-height: 1.8;
        }

        .feature-box {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 28px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 16px 18px;
            border-radius: 18px;
            backdrop-filter: blur(8px);
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(255, 255, 255, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        /* RIGHT SIDE */
        .auth-right {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #f8fafc;
            position: relative;
        }

        .login-card {
            width: 100%;
            max-width: 470px;
            background: white;
            border-radius: 28px;
            padding: 42px;
            box-shadow:
                0 10px 40px rgba(15, 23, 42, 0.08),
                0 2px 10px rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(226, 232, 240, .7);
        }

        .logo-box img {
            height: 60px;
        }

        .login-title {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .login-subtitle {
            color: #64748b;
            font-size: 15px;
            margin-bottom: 32px;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 22px;
        }

        .input-group-custom .icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 20px;
            z-index: 5;
        }

        .input-custom {
            height: 60px;
            border-radius: 16px;
            border: 1.5px solid #dbe2ea;
            background: #f8fafc;
            padding-left: 52px;
            font-size: 15px;
            transition: all .25s ease;
        }

        .input-custom:focus {
            border-color: #41BA3E;
            background: white;
            box-shadow: 0 0 0 4px rgba(65, 186, 62, .08);
        }

        .toggle-password {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #64748b;
            font-size: 20px;
        }

        .btn-login {
            height: 60px;
            border-radius: 16px;
            border: none;
            background: linear-gradient(135deg, #41BA3E, #41BA3E);
            color: white;
            font-weight: 700;
            font-size: 15px;
            transition: .3s ease;
            box-shadow: 0 10px 25px rgba(37, 99, 235, .25);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(37, 99, 235, .32);
        }

        .bottom-text {
            margin-top: 28px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }

        .bottom-text strong {
            color: #0f172a;
        }

        @media(max-width: 992px) {
            .auth-left {
                display: none;
            }

            .auth-right {
                width: 100%;
                padding: 20px;
            }

            .login-card {
                padding: 30px 24px;
                border-radius: 24px;
            }

            .login-title {
                font-size: 26px;
            }
        }
    </style>
</head>

<body>

    <div class="auth-wrapper">

        {{-- LEFT --}}
        <div class="auth-left d-lg-flex d-none align-items-center">

            <div class="auth-overlay-content">

                {{-- <h1 class="mb-12">
                    Le futur de la gestion pharmaceutique.
                </h1>

                <p class="mb-24 opacity-90">
                    L'écosystème complet pour piloter vos officines, gérer les vaccinations et centraliser vos données patients.
                </p>

                <div class="feature-box mb-16">
                    <div class="feature-icon">
                        <iconify-icon icon="solar:syringe-bold"></iconify-icon>
                    </div>

                    <div>
                        <h6 class="mb-1 text-white">Vaccination & rendez-vous</h6>
                        <small class="opacity-75">
                            Organisation fluide des campagnes et suivi des patients
                        </small>
                    </div>
                </div>

                <div class="feature-box mb-16">
                    <div class="feature-icon">
                        <i class="ri-capsule-line"></i>
                    </div>

                    <div>
                        <h6 class="mb-1 text-white">Gestion Médicaments & Stocks</h6>
                        <small class="opacity-75">
                            Base de données intelligente et mise à jour des prix.
                        </small>
                    </div>
                </div>

                <div class="feature-box mb-16">
                    <div class="feature-icon">
                        <iconify-icon icon="solar:chart-bold"></iconify-icon>
                    </div>

                    <div>
                        <h6 class="mb-1 text-white">Tableaux de bord intelligents</h6>
                        <small class="opacity-75">
                            Vue globale sur toutes vos pharmacies en temps réel.
                        </small>
                    </div>
                </div>

                <div class="feature-box">
                    <div class="feature-icon">
                        <iconify-icon icon="solar:shield-check-bold"></iconify-icon>
                    </div>

                    <div>
                        <h6 class="mb-1 text-white">Données sécurisées</h6>
                        <small class="opacity-75">
                            Protection renforcée des informations patients et pharmacie
                        </small>
                    </div>
                </div> --}}

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="auth-right">

            <div class="login-card">

                <div class="logo-box mb-4 text-center">
                    <img src="{{ URL::asset('') }}assets/images/logo.png" alt="">
                </div>

                <h2 class="login-title text-center">
                    Bon retour 👋
                </h2>

                <p class="login-subtitle text-center">
                    Connectez-vous pour accéder à votre espace pharmacie
                </p>

                @include('layouts.statuts')

                <form action="{{ url('custom-login') }}" method="POST">
                    @csrf

                    {{-- EMAIL --}}
                    <div class="input-group-custom">
                        <span class="icon">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>

                        <input type="email" required name="email" class="form-control input-custom"
                            placeholder="Adresse e-mail">
                    </div>

                    {{-- PASSWORD --}}
                    <div class="input-group-custom">

                        <span class="icon">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>

                        <input type="password" required name="password" id="your-password"
                            class="form-control input-custom" placeholder="Mot de passe">

                        <span class="toggle-password ri-eye-line" data-toggle="#your-password">
                        </span>

                    </div>

                    {{-- OPTIONS --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label text-secondary-light" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>

                        {{-- <a href="{{ url('forgot') }}"
                            class="fw-semibold text-primary text-decoration-none">
                            Mot de passe oublié ?
                        </a> --}}

                    </div>

                    {{-- BUTTON --}}
                    <button type="submit" class="btn btn-login w-100">
                        <i class="ri-login-circle-line me-1"></i>
                        Se connecter
                    </button>

                    <div class="bottom-text">
                        © {{ date('Y') }}
                        <strong>Pharmaconsults</strong>
                        • Administration sécurisée
                    </div>

                </form>

            </div>

        </div>

    </div>

    {{-- JS --}}
    <script src="{{ URL::asset('') }}assets/js/lib/jquery-3.7.1.min.js"></script>
    <script src="{{ URL::asset('') }}assets/js/lib/bootstrap.bundle.min.js"></script>
    <script src="{{ URL::asset('') }}assets/js/lib/iconify-icon.min.js"></script>

    <script>
        function initializePasswordToggle(toggleSelector) {

            $(toggleSelector).on('click', function() {

                $(this).toggleClass("ri-eye-off-line");

                var input = $($(this).attr("data-toggle"));

                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        }

        initializePasswordToggle('.toggle-password');
    </script>

</body>

</html>
