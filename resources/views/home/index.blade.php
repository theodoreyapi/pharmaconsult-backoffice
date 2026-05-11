@extends('layouts.master', ['title' => 'Tableau de bord'])

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard-main-body">

    {{-- ── Fil d'Ariane ─────────────────────────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Tableau de bord</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Tableau de bord
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Accueil</li>
        </ul>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 1 — Statistiques générales (existantes)
    ══════════════════════════════════════════════════════════════════════ --}}
    <p class="text-xs text-secondary-light fw-semibold text-uppercase mb-12 mt-4">
        <iconify-icon icon="solar:chart-2-outline" class="me-1"></iconify-icon>
        Statistiques générales
    </p>

    <div class="row row-cols-xxxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4">

        {{-- Utilisateurs --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-1 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Utilisateurs</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalUsers']) }}</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Souscriptions --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-2 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Souscriptions totales</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalSubscriptions']) }}</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="fa-solid:award" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Opérations --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-2 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Opérations totales</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalOperations']) }}</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:transfer-horizontal-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Réservations --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-2 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Réservations</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalReservations']) }}</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:calendar-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Requêtes pharmacies --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-2 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Requêtes pharmacies</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalRequests']) }}</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:question-circle-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Requêtes utilisateurs --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-2 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Requêtes utilisateurs</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalRequestsUsers']) }}</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:user-speak-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rechargements --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-2 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Rechargements réussis</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalRechargements']) }}</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:battery-charge-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transferts --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-2 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Transferts (Débit)</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalTransferts']) }}</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:card-send-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Abonnés actifs --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-3 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Abonnés actifs</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalActifSubscriptions']) }}</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="fluent:people-20-filled" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revenu rechargements --}}
        <div class="col">
            <div class="card shadow-none border bg-gradient-start-4 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Revenu total (rechargements)</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalSubscriptionAmount'], 0, ',', ' ') }} FCFA</h6>
                        </div>
                        <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /row statistiques générales --}}

    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 2 — Profils Santé & Vaccins (nouvelles cards)
    ══════════════════════════════════════════════════════════════════════ --}}
    <p class="text-xs text-secondary-light fw-semibold text-uppercase mb-12 mt-32">
        <iconify-icon icon="solar:health-outline" class="me-1"></iconify-icon>
        Profils Santé &amp; Vaccins
    </p>

    <div class="row row-cols-xxxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4">

        {{-- Profils santé créés --}}
        <div class="col">
            <div class="card shadow-none border h-100" style="border-left: 4px solid #2E7D32 !important;">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Profils santé créés</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalHealthProfiles']) }}</h6>
                            <small class="text-secondary-light">
                                dont {{ number_format($statistiques['totalActiveHealthProfiles']) }} actifs
                            </small>
                        </div>
                        <div class="w-50-px h-50-px rounded-circle d-flex justify-content-center align-items-center"
                            style="background-color: #2E7D32;">
                            <iconify-icon icon="solar:user-heart-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Abonnements profils — payés --}}
        <div class="col">
            <div class="card shadow-none border h-100" style="border-left: 4px solid #1565C0 !important;">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Abonnements profils actifs</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalPaidProfileSubscriptions']) }}</h6>
                            <small class="text-secondary-light">
                                {{ number_format($statistiques['totalPendingProfileSubscriptions']) }} en attente
                            </small>
                        </div>
                        <div class="w-50-px h-50-px rounded-circle d-flex justify-content-center align-items-center"
                            style="background-color: #1565C0;">
                            <iconify-icon icon="solar:shield-check-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revenu abonnements profils --}}
        <div class="col">
            <div class="card shadow-none border h-100" style="border-left: 4px solid #00695C !important;">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Revenu abonnements profils</p>
                            <h6 class="mb-0">
                                {{ number_format($statistiques['totalProfileSubscriptionRevenue'], 0, ',', ' ') }} FCFA
                            </h6>
                            <small class="text-secondary-light">
                                Ce mois :
                                {{ number_format($statistiques['totalProfileRevenueThisMonth'], 0, ',', ' ') }} FCFA
                            </small>
                        </div>
                        <div class="w-50-px h-50-px rounded-circle d-flex justify-content-center align-items-center"
                            style="background-color: #00695C;">
                            <iconify-icon icon="solar:wallet-money-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Vaccinations enregistrées --}}
        <div class="col">
            <div class="card shadow-none border h-100" style="border-left: 4px solid #6A1B9A !important;">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Vaccinations enregistrées</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalVaccinations']) }}</h6>
                        </div>
                        <div class="w-50-px h-50-px rounded-circle d-flex justify-content-center align-items-center"
                            style="background-color: #6A1B9A;">
                            <iconify-icon icon="solar:syringe-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Réservations vaccins --}}
        <div class="col">
            <div class="card shadow-none border h-100" style="border-left: 4px solid #E65100 !important;">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Réservations vaccins</p>
                            <h6 class="mb-0">{{ number_format($statistiques['totalVaccinAppointments']) }}</h6>
                            <small class="text-secondary-light">
                                {{ number_format($statistiques['totalPendingVaccinAppointments']) }} en attente
                            </small>
                        </div>
                        <div class="w-50-px h-50-px rounded-circle d-flex justify-content-center align-items-center"
                            style="background-color: #E65100;">
                            <iconify-icon icon="solar:calendar-add-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /row profils santé --}}

    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 3 — Graphiques
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="row gy-4 mt-4">

        {{-- Graphique 1 : Rechargements par mois --}}
        <div class="col-xxl-6 col-xl-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <h6 class="text-lg mb-0">Rechargements {{ date('Y') }}</h6>
                            <p class="text-sm text-secondary-light mt-1">Cumul mensuel des rechargements réussis</p>
                        </div>
                        <span class="badge bg-success-focus text-success-main px-12 py-6 rounded-pill fw-semibold">
                            {{ number_format(array_sum(array_column($souscriptions, 'cumulTotal')), 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                    <br>
                    <canvas id="rechargementsChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Graphique 2 : Abonnements profils par mois --}}
        <div class="col-xxl-6 col-xl-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <h6 class="text-lg mb-0">Abonnements Profils {{ date('Y') }}</h6>
                            <p class="text-sm text-secondary-light mt-1">Revenu mensuel des abonnements profils santé</p>
                        </div>
                        <span class="badge text-white px-12 py-6 rounded-pill fw-semibold"
                            style="background-color: #2E7D32;">
                            {{ number_format(array_sum(array_column($profileSubscriptions, 'cumulTotal')), 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                    <br>
                    <canvas id="profileSubsChart" height="200"></canvas>
                </div>
            </div>
        </div>

    </div>{{-- /row graphiques --}}

</div>{{-- /dashboard-main-body --}}

{{-- ── Scripts graphiques ──────────────────────────────────────────────────── --}}
<script>
// ── Helpers ──────────────────────────────────────────────────────────────────
const fcfaFormatter = value => value.toLocaleString('fr-FR') + ' FCFA';

// ── Graphique rechargements ───────────────────────────────────────────────────
(function () {
    const data   = @json($souscriptions);
    const labels = data.map(d => d.mois);
    const values = data.map(d => d.cumulTotal);

    new Chart(document.getElementById('rechargementsChart').getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label          : 'Rechargements (FCFA)',
                data           : values,
                borderColor    : '#16A34A',
                backgroundColor: 'rgba(22, 163, 74, 0.12)',
                fill           : true,
                tension        : 0.4,
                pointBackgroundColor: '#16A34A',
                pointRadius    : 4,
                borderWidth    : 2,
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + fcfaFormatter(ctx.parsed.y),
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: fcfaFormatter },
                    grid: { color: 'rgba(0,0,0,0.04)' },
                },
                x: {
                    grid: { display: false },
                },
            },
        },
    });
})();

// ── Graphique abonnements profils ─────────────────────────────────────────────
(function () {
    const data    = @json($profileSubscriptions);
    const labels  = data.map(d => d.mois);
    const revenue = data.map(d => d.cumulTotal);
    const counts  = data.map(d => d.nbr);

    new Chart(document.getElementById('profileSubsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label          : 'Revenu (FCFA)',
                    data           : revenue,
                    backgroundColor: 'rgba(46, 125, 50, 0.75)',
                    borderColor    : '#2E7D32',
                    borderWidth    : 1,
                    borderRadius   : 4,
                    yAxisID        : 'yRevenu',
                },
                {
                    label          : 'Nombre d\'abonnements',
                    data           : counts,
                    type           : 'line',
                    borderColor    : '#1565C0',
                    backgroundColor: 'rgba(21, 101, 192, 0.1)',
                    fill           : false,
                    tension        : 0.3,
                    pointBackgroundColor: '#1565C0',
                    pointRadius    : 4,
                    borderWidth    : 2,
                    yAxisID        : 'yCount',
                },
            ],
        },
        options: {
            responsive: true,
            interaction: {
                mode : 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display : true,
                    position: 'top',
                    labels  : { boxWidth: 12, font: { size: 11 } },
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.datasetIndex === 0
                            ? ' Revenu : ' + fcfaFormatter(ctx.parsed.y)
                            : ' Abonnements : ' + ctx.parsed.y,
                    },
                },
            },
            scales: {
                yRevenu: {
                    type       : 'linear',
                    position   : 'left',
                    beginAtZero: true,
                    ticks      : { callback: fcfaFormatter, font: { size: 10 } },
                    grid       : { color: 'rgba(0,0,0,0.04)' },
                    title      : { display: true, text: 'Revenu (FCFA)', font: { size: 11 } },
                },
                yCount: {
                    type       : 'linear',
                    position   : 'right',
                    beginAtZero: true,
                    ticks      : { stepSize: 1, font: { size: 10 } },
                    grid       : { drawOnChartArea: false },
                    title      : { display: true, text: 'Nb abonnements', font: { size: 11 } },
                },
                x: {
                    grid: { display: false },
                },
            },
        },
    });
})();
</script>

@endsection
