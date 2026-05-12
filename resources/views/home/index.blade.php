@extends('layouts.master', ['title' => 'Tableau de bord'])

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .dashboard-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 45%, #2563eb 100%);
            border-radius: 24px;
            overflow: hidden;
            position: relative;
            padding: 32px;
        }

        .dashboard-hero::before {
            content: '';
            position: absolute;
            right: -60px;
            top: -60px;
            width: 220px;
            height: 220px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .dashboard-hero::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .hero-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 12px 18px;
            color: white;
        }

        .dashboard-card {
            border: 0;
            border-radius: 22px;
            overflow: hidden;
            transition: 0.3s ease;
            position: relative;
        }

        .dashboard-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 25px 40px rgba(0, 0, 0, 0.08);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 5px;
            top: 0;
            left: 0;
        }

        .gradient-blue::before {
            background: linear-gradient(90deg, #2563eb, #60a5fa);
        }

        .gradient-green::before {
            background: linear-gradient(90deg, #16a34a, #4ade80);
        }

        .gradient-purple::before {
            background: linear-gradient(90deg, #7c3aed, #a78bfa);
        }

        .gradient-orange::before {
            background: linear-gradient(90deg, #ea580c, #fb923c);
        }

        .gradient-pink::before {
            background: linear-gradient(90deg, #db2777, #f472b6);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
        }

        .bg-blue-soft {
            background: linear-gradient(135deg, #2563eb, #60a5fa);
        }

        .bg-green-soft {
            background: linear-gradient(135deg, #16a34a, #4ade80);
        }

        .bg-purple-soft {
            background: linear-gradient(135deg, #7c3aed, #a78bfa);
        }

        .bg-orange-soft {
            background: linear-gradient(135deg, #ea580c, #fb923c);
        }

        .bg-pink-soft {
            background: linear-gradient(135deg, #db2777, #f472b6);
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 18px;
        }

        .chart-card {
            border-radius: 24px;
            border: 0;
        }

        .mini-badge {
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .top-vaccine-item {
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .top-vaccine-item:last-child {
            border-bottom: 0;
        }

        .profile-avatar {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, #2563eb, #60a5fa);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>

    <div class="dashboard-main-body">

        {{-- HERO --}}
        <div class="dashboard-hero mb-28">

            <div class="row align-items-center gy-4 position-relative">

                <div class="col-lg-8">

                    <div class="d-inline-flex align-items-center gap-2 hero-badge mb-20">
                        <iconify-icon icon="solar:shield-check-bold"></iconify-icon>
                        <span>Plateforme Pharmaceutique Intelligente</span>
                    </div>

                    <h2 class="text-white fw-bold mb-14">
                        Bienvenue sur votre tableau de bord Pharmaconsults
                    </h2>

                    <p class="text-white opacity-75 mb-0 fs-6">
                        Analysez vos statistiques, surveillez vos vaccinations,
                        gérez vos pharmacies et suivez l’évolution globale
                        de votre plateforme en temps réel.
                    </p>

                </div>

                <div class="col-lg-4">

                    <div class="row gy-3">

                        <div class="col-6">
                            <div class="hero-badge text-center">
                                <h3 class="text-white mb-1">
                                    {{ number_format($statistiques['totalUsers']) }}
                                </h3>
                                <small>Utilisateurs</small>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="hero-badge text-center">
                                <h3 class="text-white mb-1">
                                    {{ number_format($statistiques['totalHealthProfiles']) }}
                                </h3>
                                <small>Profils santé</small>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- SECTION GENERAL --}}
        <div class="section-title">
            <iconify-icon icon="solar:chart-2-bold"></iconify-icon>
            Statistiques Générales
        </div>

        <div class="row gy-4">

            {{-- ═══════════════════════════════════════
        SECTION — UTILISATEURS & ACTIVITÉ
    ═══════════════════════════════════════ --}}
            <div class="col-12">
                <div class="d-flex align-items-center gap-2 mb-2 mt-2">
                    <div class="dashboard-section-dot bg-primary"></div>
                    <h6 class="mb-0 fw-bold">Utilisateurs & Activité</h6>
                </div>
            </div>

            {{-- USERS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-blue h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Utilisateurs
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalUsers']) }}
                                </h3>

                                <span class="text-success-main text-sm">
                                    +{{ number_format($statistiques['totalUsersThisMonth']) }}
                                    ce mois
                                </span>
                            </div>

                            <div class="stat-icon bg-blue-soft">
                                <iconify-icon icon="solar:users-group-rounded-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- ABONNÉS ACTIFS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-cyan h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Abonnés actifs
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalActifSubscriptions']) }}
                                </h3>

                                <span class="text-info-main text-sm">
                                    comptes premium
                                </span>
                            </div>

                            <div class="stat-icon bg-cyan-soft">
                                <iconify-icon icon="fluent:people-20-filled"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- REQUÊTES PHARMACIES --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-indigo h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Requêtes pharmacies
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalRequests']) }}
                                </h3>

                                <span class="text-primary-main text-sm">
                                    demandes envoyées
                                </span>
                            </div>

                            <div class="stat-icon bg-indigo-soft">
                                <iconify-icon icon="solar:question-circle-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- REQUÊTES UTILISATEURS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-dark h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Requêtes utilisateurs
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalRequestsUsers']) }}
                                </h3>

                                <span class="text-warning-main text-sm">
                                    interactions utilisateurs
                                </span>
                            </div>

                            <div class="stat-icon bg-dark-soft">
                                <iconify-icon icon="solar:user-speak-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            {{-- ═══════════════════════════════════════
        SECTION — TRANSACTIONS & FINANCE
    ═══════════════════════════════════════ --}}
            <div class="col-12">
                <div class="d-flex align-items-center gap-2 mb-2 mt-3">
                    <div class="dashboard-section-dot bg-success"></div>
                    <h6 class="mb-0 fw-bold">Transactions & Finance</h6>
                </div>
            </div>

            {{-- SOUSCRIPTIONS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-purple h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Souscriptions
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalSubscriptions']) }}
                                </h3>

                                <span class="text-success-main text-sm">
                                    abonnements totaux
                                </span>
                            </div>

                            <div class="stat-icon bg-purple-soft">
                                <iconify-icon icon="fa-solid:award"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- OPÉRATIONS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-orange h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Opérations
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalOperations']) }}
                                </h3>

                                <span class="text-warning-main text-sm">
                                    transactions effectuées
                                </span>
                            </div>

                            <div class="stat-icon bg-orange-soft">
                                <iconify-icon icon="solar:transfer-horizontal-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- TRANSFERTS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-red h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Transferts Débit
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalTransferts']) }}
                                </h3>

                                <span class="text-danger-main text-sm">
                                    transferts réalisés
                                </span>
                            </div>

                            <div class="stat-icon bg-red-soft">
                                <iconify-icon icon="solar:card-send-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- RECHARGEMENTS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-green h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Rechargements
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalRechargements']) }}
                                </h3>

                                <span class="text-success-main text-sm">
                                    réussis
                                </span>
                            </div>

                            <div class="stat-icon bg-green-soft">
                                <iconify-icon icon="solar:battery-charge-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- REVENUS --}}
            <div class="col-xxl-6 col-lg-6 col-sm-12">
                <div class="card dashboard-card gradient-gold h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Revenus Globaux
                                </p>

                                <h2 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalGlobalRevenue'], 0, ',', ' ') }}
                                    FCFA
                                </h2>

                                <span class="text-success-main text-sm">
                                    revenus plateforme
                                </span>
                            </div>

                            <div class="stat-icon bg-gold-soft">
                                <iconify-icon icon="solar:wallet-money-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            {{-- ═══════════════════════════════════════
        SECTION — SANTÉ & VACCINS
    ═══════════════════════════════════════ --}}
            <div class="col-12">
                <div class="d-flex align-items-center gap-2 mb-2 mt-3">
                    <div class="dashboard-section-dot bg-danger"></div>
                    <h6 class="mb-0 fw-bold">Santé & Vaccination</h6>
                </div>
            </div>

            {{-- PROFILS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-green h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Profils Santé
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalHealthProfiles']) }}
                                </h3>

                                <span class="text-success-main text-sm">
                                    {{ number_format($statistiques['totalActiveHealthProfiles']) }}
                                    actifs
                                </span>
                            </div>

                            <div class="stat-icon bg-green-soft">
                                <iconify-icon icon="solar:heart-pulse-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- VACCINATIONS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-purple h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Vaccinations
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalVaccinations']) }}
                                </h3>

                                <span class="text-success-main text-sm">
                                    +{{ number_format($statistiques['totalVaccinationsThisMonth']) }}
                                    ce mois
                                </span>
                            </div>

                            <div class="stat-icon bg-purple-soft">
                                <iconify-icon icon="solar:syringe-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- RENDEZ-VOUS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-orange h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    RDV Vaccins
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalVaccinAppointments']) }}
                                </h3>

                                <span class="text-warning-main text-sm">
                                    {{ number_format($statistiques['totalPendingVaccinAppointments']) }}
                                    en attente
                                </span>
                            </div>

                            <div class="stat-icon bg-orange-soft">
                                <iconify-icon icon="solar:calendar-add-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- ABONNEMENTS PROFILS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-cyan h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Abonnements Profils
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalPaidProfileSubscriptions']) }}
                                </h3>

                                <span class="text-info-main text-sm">
                                    actifs payés
                                </span>
                            </div>

                            <div class="stat-icon bg-cyan-soft">
                                <iconify-icon icon="solar:shield-check-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- SECTION PROFILS --}}
        <div class="section-title mt-32">
            <iconify-icon icon="solar:shield-user-bold"></iconify-icon>
            Analyse des profils santé
        </div>

        <div class="row gy-4">

            {{-- ═══════════════════════════════════════
        SECTION — PROFILS SANTÉ
    ═══════════════════════════════════════ --}}
            <div class="col-12">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="dashboard-section-dot bg-success"></div>
                    <h6 class="mb-0 fw-bold">Profils Santé</h6>
                </div>
            </div>

            {{-- PROFILS SANTÉ --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-green h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Profils Santé
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalHealthProfiles']) }}
                                </h3>

                                <span class="text-success-main text-sm">
                                    {{ number_format($statistiques['totalActiveHealthProfiles']) }}
                                    actifs
                                </span>
                            </div>

                            <div class="stat-icon bg-green-soft">
                                <iconify-icon icon="solar:heart-pulse-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- PROFILS FÉMININS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-pink h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Profils féminins
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalFemaleProfiles']) }}
                                </h3>

                                <span class="text-pink-main text-sm">
                                    femmes enregistrées
                                </span>
                            </div>

                            <div class="stat-icon bg-pink-soft">
                                <iconify-icon icon="solar:woman-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- PROFILS MASCULINS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-blue h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Profils masculins
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalMaleProfiles']) }}
                                </h3>

                                <span class="text-primary-main text-sm">
                                    hommes enregistrés
                                </span>
                            </div>

                            <div class="stat-icon bg-blue-soft">
                                <iconify-icon icon="solar:men-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- ENFANTS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-cyan h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Enfants
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalChildrenProfiles']) }}
                                </h3>

                                <span class="text-info-main text-sm">
                                    profils mineurs
                                </span>
                            </div>

                            <div class="stat-icon bg-cyan-soft">
                                <iconify-icon icon="solar:baby-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- VOYAGEURS --}}
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-orange h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Voyageurs
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalTravelerProfiles']) }}
                                </h3>

                                <span class="text-warning-main text-sm">
                                    profils internationaux
                                </span>
                            </div>

                            <div class="stat-icon bg-orange-soft">
                                <iconify-icon icon="solar:global-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            {{-- ═══════════════════════════════════════
        SECTION — ABONNEMENTS & REVENUS
    ═══════════════════════════════════════ --}}
            <div class="col-12">
                <div class="d-flex align-items-center gap-2 mb-2 mt-3">
                    <div class="dashboard-section-dot bg-primary"></div>
                    <h6 class="mb-0 fw-bold">Abonnements & Revenus</h6>
                </div>
            </div>

            {{-- ABONNEMENTS ACTIFS --}}
            <div class="col-xxl-4 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-indigo h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Abonnements actifs
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalPaidProfileSubscriptions']) }}
                                </h3>

                                <span class="text-warning-main text-sm">
                                    {{ number_format($statistiques['totalPendingProfileSubscriptions']) }}
                                    en attente
                                </span>
                            </div>

                            <div class="stat-icon bg-indigo-soft">
                                <iconify-icon icon="solar:shield-check-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- REVENUS ABONNEMENTS --}}
            <div class="col-xxl-4 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-gold h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Revenus abonnements
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalProfileSubscriptionRevenue'], 0, ',', ' ') }}
                                </h3>

                                <span class="text-success-main text-sm">
                                    FCFA générés
                                </span>
                            </div>

                            <div class="stat-icon bg-gold-soft">
                                <iconify-icon icon="solar:wallet-money-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- REVENUS CE MOIS --}}
            <div class="col-xxl-4 col-lg-4 col-sm-6">
                <div class="card dashboard-card gradient-green h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Revenus ce mois
                                </p>

                                <h3 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalProfileRevenueThisMonth'], 0, ',', ' ') }}
                                </h3>

                                <span class="text-success-main text-sm">
                                    FCFA ce mois-ci
                                </span>
                            </div>

                            <div class="stat-icon bg-green-soft">
                                <iconify-icon icon="solar:chart-2-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            {{-- ═══════════════════════════════════════
        SECTION — VACCINATION
    ═══════════════════════════════════════ --}}
            <div class="col-12">
                <div class="d-flex align-items-center gap-2 mb-2 mt-3">
                    <div class="dashboard-section-dot bg-danger"></div>
                    <h6 class="mb-0 fw-bold">Vaccination & Rendez-vous</h6>
                </div>
            </div>

            {{-- VACCINATIONS --}}
            <div class="col-xxl-6 col-lg-6 col-sm-6">
                <div class="card dashboard-card gradient-purple h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Vaccinations enregistrées
                                </p>

                                <h2 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalVaccinations']) }}
                                </h2>

                                <span class="text-success-main text-sm">
                                    vaccins administrés
                                </span>
                            </div>

                            <div class="stat-icon bg-purple-soft">
                                <iconify-icon icon="solar:syringe-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- RENDEZ-VOUS --}}
            <div class="col-xxl-6 col-lg-6 col-sm-6">
                <div class="card dashboard-card gradient-red h-100">
                    <div class="card-body p-24">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <p class="text-secondary-light mb-8">
                                    Réservations vaccins
                                </p>

                                <h2 class="fw-bold mb-6">
                                    {{ number_format($statistiques['totalVaccinAppointments']) }}
                                </h2>

                                <span class="text-warning-main text-sm">
                                    {{ number_format($statistiques['totalPendingVaccinAppointments']) }}
                                    en attente
                                </span>
                            </div>

                            <div class="stat-icon bg-red-soft">
                                <iconify-icon icon="solar:calendar-add-bold"></iconify-icon>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- ═════════════════════════════════════════════════════════════
    SECTION — ANALYTICS & CHARTS
═════════════════════════════════════════════════════════════ --}}
        <div class="row gy-4 mt-4">

            {{-- RECHARGEMENTS --}}
            <div class="col-xxl-4 col-xl-6">

                <div class="card dashboard-chart-card h-100 border-0">

                    <div class="card-body p-28">

                        <div class="d-flex justify-content-between align-items-start mb-24">

                            <div>
                                <span class="dashboard-chart-label bg-success-soft text-success-main">
                                    Revenus
                                </span>

                                <h5 class="fw-bold mt-14 mb-8">
                                    Rechargements {{ date('Y') }}
                                </h5>

                                <p class="text-secondary-light mb-0">
                                    Evolution mensuelle des revenus
                                </p>
                            </div>

                            <div class="chart-total-box success">

                                <h6 class="mb-4">
                                    {{ number_format($statistiques['totalSubscriptionAmount'], 0, ',', ' ') }}
                                </h6>

                                <span>FCFA</span>

                            </div>

                        </div>

                        <canvas id="rechargementsChart" height="140"></canvas>

                    </div>

                </div>

            </div>

            {{-- VACCINATIONS --}}
            <div class="col-xxl-4 col-xl-6">

                <div class="card dashboard-chart-card h-100 border-0">

                    <div class="card-body p-28">

                        <div class="d-flex justify-content-between align-items-start mb-24">

                            <div>
                                <span class="dashboard-chart-label bg-purple-soft text-purple">
                                    Santé
                                </span>

                                <h5 class="fw-bold mt-14 mb-8">
                                    Vaccinations {{ date('Y') }}
                                </h5>

                                <p class="text-secondary-light mb-0">
                                    Vaccinations enregistrées par mois
                                </p>
                            </div>

                            <div class="chart-total-box purple">

                                <h6 class="mb-4">
                                    {{ number_format($statistiques['totalVaccinations']) }}
                                </h6>

                                <span>Vaccins</span>

                            </div>

                        </div>

                        <canvas id="vaccinationChart" height="140"></canvas>

                    </div>

                </div>

            </div>

            {{-- ABONNEMENTS PROFILS --}}
            <div class="col-xxl-4 col-xl-12">

                <div class="card dashboard-chart-card h-100 border-0">

                    <div class="card-body p-28">

                        <div class="d-flex justify-content-between align-items-start mb-24">

                            <div>
                                <span class="dashboard-chart-label bg-blue-soft text-primary">
                                    Abonnements
                                </span>

                                <h5 class="fw-bold mt-14 mb-8">
                                    Profils Santé {{ date('Y') }}
                                </h5>

                                <p class="text-secondary-light mb-0">
                                    Revenus & nombre d'abonnements
                                </p>
                            </div>

                            <div class="chart-total-box blue">

                                <h6 class="mb-4">
                                    {{ number_format(array_sum(array_column($profileSubscriptions, 'cumulTotal')), 0, ',', ' ') }}
                                </h6>

                                <span>FCFA</span>

                            </div>

                        </div>

                        <canvas id="profileSubsChart" height="140"></canvas>

                    </div>

                </div>

            </div>

        </div>

        {{-- ═════════════════════════════════════════════════════════════
    STYLES
═════════════════════════════════════════════════════════════ --}}
        <style>
            .chart-total-box {
                min-width: 120px;
                text-align: center;
                padding: 14px;
                border-radius: 16px;
            }

            .chart-total-box h6 {
                font-size: 18px;
                font-weight: 700;
            }

            .chart-total-box span {
                font-size: 12px;
                opacity: .8;
            }

            .chart-total-box.success {
                background: rgba(22, 163, 74, .10);
                color: #16a34a;
            }

            .chart-total-box.purple {
                background: rgba(124, 58, 237, .10);
                color: #7c3aed;
            }

            .chart-total-box.blue {
                background: rgba(37, 99, 235, .10);
                color: #2563eb;
            }
        </style>

        {{-- ═════════════════════════════════════════════════════════════
    CHARTS JS
═════════════════════════════════════════════════════════════ --}}
        <script>
            const fcfaFormatter = value =>
                value.toLocaleString('fr-FR') + ' FCFA';

            Chart.defaults.font.family = 'Inter';
            Chart.defaults.color = '#6b7280';


            // ═════════════════════════════════════════════════════════════
            // RECHARGEMENTS
            // ═════════════════════════════════════════════════════════════
            (function() {

                const data = @json($souscriptions);

                new Chart(document.getElementById('rechargementsChart'), {

                    type: 'line',

                    data: {
                        labels: data.map(i => i.mois),

                        datasets: [{
                            label: 'Revenus',

                            data: data.map(i => i.cumulTotal),

                            borderColor: '#16a34a',

                            backgroundColor: 'rgba(22,163,74,0.10)',

                            fill: true,

                            tension: 0.4,

                            borderWidth: 3,

                            pointRadius: 4,

                            pointHoverRadius: 6,

                            pointBackgroundColor: '#16a34a',
                        }]
                    },

                    options: {

                        responsive: true,

                        plugins: {

                            legend: {
                                display: false
                            },

                            tooltip: {
                                backgroundColor: '#111827',

                                callbacks: {
                                    label: ctx =>
                                        fcfaFormatter(ctx.parsed.y)
                                }
                            }
                        },

                        scales: {

                            x: {
                                grid: {
                                    display: false
                                }
                            },

                            y: {
                                beginAtZero: true,

                                ticks: {
                                    callback: value =>
                                        value.toLocaleString('fr-FR')
                                },

                                grid: {
                                    color: 'rgba(0,0,0,0.04)'
                                }
                            }
                        }
                    }
                });

            })();


            // ═════════════════════════════════════════════════════════════
            // VACCINATIONS
            // ═════════════════════════════════════════════════════════════
            (function() {

                const data = @json($vaccinationsStats);

                new Chart(document.getElementById('vaccinationChart'), {

                    type: 'bar',

                    data: {
                        labels: data.map(i => i.mois),

                        datasets: [{
                            label: 'Vaccinations',

                            data: data.map(i => i.total),

                            backgroundColor: '#7c3aed',

                            borderRadius: 10,

                            maxBarThickness: 40
                        }]
                    },

                    options: {

                        responsive: true,

                        plugins: {

                            legend: {
                                display: false
                            },

                            tooltip: {
                                backgroundColor: '#111827',
                            }
                        },

                        scales: {

                            x: {
                                grid: {
                                    display: false
                                }
                            },

                            y: {
                                beginAtZero: true,

                                grid: {
                                    color: 'rgba(0,0,0,0.04)'
                                }
                            }
                        }
                    }
                });

            })();


            // ═════════════════════════════════════════════════════════════
            // ABONNEMENTS PROFILS
            // ═════════════════════════════════════════════════════════════
            (function() {

                const data = @json($profileSubscriptions);

                new Chart(document.getElementById('profileSubsChart'), {

                    type: 'bar',

                    data: {

                        labels: data.map(d => d.mois),

                        datasets: [

                            {
                                label: 'Revenus',

                                data: data.map(d => d.cumulTotal),

                                backgroundColor: 'rgba(37,99,235,.75)',

                                borderRadius: 8,

                                yAxisID: 'yRevenue',
                            },

                            {
                                label: 'Abonnements',

                                data: data.map(d => d.nbr),

                                type: 'line',

                                borderColor: '#16a34a',

                                backgroundColor: 'rgba(22,163,74,.10)',

                                fill: false,

                                tension: 0.4,

                                pointRadius: 4,

                                borderWidth: 3,

                                yAxisID: 'yCount',
                            }
                        ]
                    },

                    options: {

                        responsive: true,

                        interaction: {
                            mode: 'index',
                            intersect: false
                        },

                        plugins: {

                            legend: {
                                position: 'top'
                            },

                            tooltip: {

                                backgroundColor: '#111827',

                                callbacks: {

                                    label: ctx =>

                                        ctx.datasetIndex === 0 ?
                                        ' Revenus : ' + fcfaFormatter(ctx.parsed.y) : ' Abonnements : ' + ctx.parsed
                                        .y
                                }
                            }
                        },

                        scales: {

                            x: {
                                grid: {
                                    display: false
                                }
                            },

                            yRevenue: {

                                type: 'linear',

                                position: 'left',

                                beginAtZero: true,

                                ticks: {
                                    callback: value =>
                                        value.toLocaleString('fr-FR')
                                },

                                grid: {
                                    color: 'rgba(0,0,0,0.04)'
                                }
                            },

                            yCount: {

                                type: 'linear',

                                position: 'right',

                                beginAtZero: true,

                                grid: {
                                    drawOnChartArea: false
                                },

                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

            })();
        </script>

        {{-- TABLEAUX --}}
        <div class="row gy-4 mt-10">

            {{-- TOP VACCINS --}}
            <div class="col-xl-6">

                <div class="card chart-card h-100">

                    <div class="card-body p-28">

                        <div class="d-flex justify-content-between align-items-center mb-20">

                            <div>
                                <h5 class="fw-bold mb-0">
                                    Vaccins populaires
                                </h5>
                            </div>

                            <span class="mini-badge bg-warning-focus text-warning-main">
                                Top 5
                            </span>

                        </div>

                        @forelse($topVaccines as $vaccine)
                            <div class="top-vaccine-item">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>

                                        <h6 class="mb-4">
                                            {{ $vaccine->name }}
                                        </h6>

                                        <small class="text-secondary-light">
                                            Vaccinations enregistrées
                                        </small>

                                    </div>

                                    <span class="fw-bold text-primary-600">
                                        {{ number_format($vaccine->total) }}
                                    </span>

                                </div>

                            </div>

                        @empty

                            <div class="text-center py-5">
                                <iconify-icon icon="solar:syringe-outline" class="text-5xl text-secondary-light mb-3">
                                </iconify-icon>

                                <p class="mb-0 text-secondary-light">
                                    Aucun vaccin enregistré
                                </p>
                            </div>
                        @endforelse

                    </div>

                </div>

            </div>

            {{-- DERNIERS PROFILS --}}
            <div class="col-xl-6">

                <div class="card chart-card h-100">

                    <div class="card-body p-28">

                        <div class="d-flex justify-content-between align-items-center mb-20">

                            <div>
                                <h5 class="fw-bold mb-0">
                                    Derniers profils santé
                                </h5>
                            </div>

                            <span class="mini-badge bg-info-focus text-info-main">
                                Récent
                            </span>

                        </div>

                        @forelse($latestProfiles as $profile)
                            <div class="top-vaccine-item">

                                <div class="d-flex align-items-center justify-content-between">

                                    <div class="d-flex align-items-center gap-3">

                                        <div class="profile-avatar">
                                            {{ strtoupper(substr($profile->name, 0, 1)) }}
                                        </div>

                                        <div>

                                            <h6 class="mb-2">
                                                {{ $profile->name }}
                                            </h6>

                                            <small class="text-secondary-light">
                                                {{ ucfirst($profile->profile_type) }}
                                                •
                                                {{ $profile->gender }}
                                            </small>

                                        </div>

                                    </div>

                                    <span class="text-sm text-secondary-light">
                                        {{ \Carbon\Carbon::parse($profile->created_at)->diffForHumans() }}
                                    </span>

                                </div>

                            </div>

                        @empty

                            <div class="text-center py-5">
                                <iconify-icon icon="solar:user-outline" class="text-5xl text-secondary-light mb-3">
                                </iconify-icon>

                                <p class="mb-0 text-secondary-light">
                                    Aucun profil santé
                                </p>
                            </div>
                        @endforelse

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- CHARTS --}}
@endsection
