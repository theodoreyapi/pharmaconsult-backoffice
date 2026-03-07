@extends('layouts.master', ['title' => 'Tableau de bord'])

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="dashboard-main-body">
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

        <div class="row row-cols-xxxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4">
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-1 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Nombre total d'utilisateurs</p>
                                <h6 class="mb-0">{{ $statistiques['totalUsers'] }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="gridicons:multiple-users"
                                    class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-2 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Souscriptions totales</p>
                                <h6 class="mb-0">{{ $statistiques['totalSubscriptions'] }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="fa-solid:award" class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-2 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Operations totales</p>
                                <h6 class="mb-0">{{ $statistiques['totalOperations'] }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="fa-solid:award" class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-2 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Reservations totales</p>
                                <h6 class="mb-0">{{ $statistiques['totalReservations'] }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="fa-solid:award" class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-2 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Requetes totales</p>
                                <h6 class="mb-0">{{ $statistiques['totalRequests'] }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="fa-solid:award" class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-2 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Requetes totales Utilisateurs</p>
                                <h6 class="mb-0">{{ $statistiques['totalRequestsUsers'] }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="fa-solid:award" class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-2 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Rechargement total</p>
                                <h6 class="mb-0">{{ $statistiques['totalRechargements'] }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="fa-solid:award" class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-2 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Transfert total</p>
                                <h6 class="mb-0">{{ $statistiques['totalTransferts'] }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="fa-solid:award" class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-3 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Nbre total utilisateurs souscrits</p>
                                <h6 class="mb-0">{{ $statistiques['totalActifSubscriptions'] }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="fluent:people-20-filled"
                                    class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-4 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Revenu total</p>
                                <h6 class="mb-0">{{ number_format($statistiques['totalSubscriptionAmount'], 0, ',', ' ') }} FCFA</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
        </div>

        <div class="row gy-4 mt-1">
            <div class="col-xxl-12 col-xl-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <h6 class="text-lg mb-0">Statistiques des souscriptions {{ date('Y') }}</h6>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-8">
                            @php
                                $total = array_sum(array_column($souscriptions, 'cumulTotal'));
                            @endphp
                            <h6 class="mb-0 text-primary">{{ number_format($total, 0, ',', ' ') }} FCFA</h6>
                            <br>
                        </div>
                        <br>

                        <canvas id="statistiquesChart"></canvas>

                        <script>
                            const statistiques = @json($souscriptions);

                            const labels = statistiques.map(item => item.mois);
                            const dataValues = statistiques.map(item => item.cumulTotal);

                            const ctx = document.getElementById('statistiquesChart').getContext('2d');

                            new Chart(ctx, {
                                type: 'line', // type de graphique
                                data: {
                                    labels: labels, // Mois
                                    datasets: [{
                                        label: 'Cumul des abonnements',
                                        data: dataValues, // Valeurs
                                        borderColor: 'green',
                                        backgroundColor: 'rgba(0, 200, 83, 0.2)',
                                        fill: true,
                                        tension: 0.4 // rend la courbe fluide
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function(value) {
                                                    return value.toLocaleString() + ' FCFA'; // formatage en FCFA
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
            {{-- <div class="col-xxl-6 col-xl-6">
                <div class="card h-100 radius-8 border">
                    <div class="card-body p-24">
                        <h6 class="mb-12 fw-semibold text-lg mb-16">Nombre total d'abonnés</h6>
                        <div class="d-flex align-items-center gap-2 mb-20">
                            <h6 class="fw-semibold mb-0">5,000</h6>
                            <p class="text-sm mb-0">
                                <span
                                    class="bg-danger-focus border br-danger px-8 py-2 rounded-pill fw-semibold text-danger-main text-sm d-inline-flex align-items-center gap-1">
                                    10%
                                    <iconify-icon icon="iconamoon:arrow-down-2-fill" class="icon"></iconify-icon>
                                </span>
                                - 20 Per Day
                            </p>
                        </div>

                        <div id="barChart"></div>

                    </div>
                </div>
            </div>
            <div class="col-xxl-9 col-xl-12">
                <div class="card h-100">
                    <div class="card-body p-24">
                        <div class="" role="tabpanel" aria-labelledby="pills-recent-leads-tab" tabindex="0">
                            <div class="table-responsive scroll-sm">
                                <table class="table bordered-table sm-table mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Users </th>
                                            <th scope="col">Registered On</th>
                                            <th scope="col">Plan</th>
                                            <th scope="col" class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ URL::asset('') }}assets/images/users/user1.png"
                                                        alt=""
                                                        class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                                    <div class="flex-grow-1">
                                                        <h6 class="text-md mb-0 fw-medium">Dianne Russell</h6>
                                                        <span
                                                            class="text-sm text-secondary-light fw-medium">redaniel@gmail.com</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>27 Mar 2024</td>
                                            <td>Free</td>
                                            <td class="text-center">
                                                <span
                                                    class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Top Performer</h6>
                            <a href="javascript:void(0)"
                                class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                                View All
                                <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                            </a>
                        </div>

                        <div class="mt-32">

                            <div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                                <div class="d-flex align-items-center">
                                    <img src="{{ URL::asset('') }}assets/images/users/user1.png" alt=""
                                        class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                    <div class="flex-grow-1">
                                        <h6 class="text-md mb-0 fw-medium">Dianne Russell</h6>
                                        <span class="text-sm text-secondary-light fw-medium">Agent ID: 36254</span>
                                    </div>
                                </div>
                                <span class="text-primary-light text-md fw-medium">$20</span>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <div class="col-xxl-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Generated Content</h6>
                            <select class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                                <option>Today</option>
                                <option>Weekly</option>
                                <option>Monthly</option>
                                <option>Yearly</option>
                            </select>
                        </div>

                        <ul class="d-flex flex-wrap align-items-center mt-3 gap-3">
                            <li class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-12-px rounded-circle bg-primary-600"></span>
                                <span class="text-secondary-light text-sm fw-semibold">Word:
                                    <span class="text-primary-light fw-bold">500</span>
                                </span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-12-px rounded-circle bg-yellow"></span>
                                <span class="text-secondary-light text-sm fw-semibold">Image:
                                    <span class="text-primary-light fw-bold">300</span>
                                </span>
                            </li>
                        </ul>

                        <div class="mt-40">
                            <div id="paymentStatusChart" class="margin-16-minus"></div>
                        </div>

                    </div>
                </div>
            </div> --}}
        </div>
    </div>
@endsection
