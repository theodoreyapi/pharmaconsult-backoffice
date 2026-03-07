@extends('layouts.master', ['title' => 'Rechargement'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>

    <script>
        document.getElementById('rechargementForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const money = document.getElementById('money').value;
            const token = document.querySelector('input[name="_token"]').value;

            fetch("{{ route('rechargement.init') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        money: money
                    })
                })
                .then(res => res.json())
                .then(data => {
                    console.log(data);
                    const resultContent = document.getElementById('resultContent');

                    if (data.status) {
                        resultContent.innerHTML = `
                <iframe src="${data.url}" width="100%" height="600px"></iframe>
            `;
                    } else {
                        resultContent.innerHTML = `<p class="text-danger">${data.message}</p>`;
                    }

                    new bootstrap.Modal(document.getElementById('resultModal')).show();
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('resultContent').innerHTML =
                        `<p class="text-danger">Erreur de connexion : ${err.message}</p>`;
                    new bootstrap.Modal(document.getElementById('resultModal')).show();
                });
        });
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Rechargement</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste des Rechargements</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="col-4 mb-24">
            <div class="card shadow-none border bg-gradient-start-4 h-100">
                <div class="card-body p-20">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <p class="fw-medium text-primary-light mb-1">Solde</p>
                            <h6 class="mb-0">{{ number_format($wallets, 0, ',', ' ') }} FCFA</h6>
                        </div>
                        <div
                            class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                            <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div><!-- card end -->
        </div>

        <div class="card h-100 p-0 radius-12">
            {{-- <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                        <option>Status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>
            </div> --}}
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3">
                </div>
                <a href="#"
                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#addExampleModal">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Recharger le portefeuille
                </a>
            </div>
            <div class="modal fade" id="addExampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                    <div class="modal-content radius-16 bg-base">
                        <div
                            class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0 bg-success text-white">
                            <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">
                                Rechargement du portefeuille
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="color: white"></button>
                        </div>
                        <div class="modal-body p-24">
                            <form id="rechargementForm" action="javascript:void(0);" method="post" role="form">
                                @csrf
                                <div class="row">
                                    <div class="alert alert-warning" role="alert">
                                        <strong>Information!</strong> Le montant maximum autorisé pour un rechargement est
                                        de
                                        <strong>200 000 FCFA</strong>.
                                    </div>
                                    <div class="col-12 mb-20">
                                        <label for="name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Montant
                                        </label>
                                        <input type="number" name="money" class="form-control radius-8" id="money"
                                            placeholder="Montant">
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                        <button type="reset" data-bs-dismiss="modal" aria-label="Close"
                                            class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                            Annuler
                                        </button>
                                        <button type="submit"
                                            class="btn btn-primary border border-primary-600 text-md px-50 py-12 radius-8">
                                            Recharger
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal résultat -->
            <div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-3">
                        <div class="modal-header">
                            <h5 class="modal-title">Résultat du Rechargement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="resultContent"></div>
                    </div>
                </div>
            </div>

            <div class="card-body p-24">
                <div class="table-responsive scroll-sm">
                    @if ($requetes == null || count($requetes) == 0)
                        <tr>
                            <td colspan="5" class="text-center">
                                Aucun rechargement trouvé.
                            </td>
                        </tr>
                    @else
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th scope="col">Montant</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requetes as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item['amount'] }} FCFA</strong>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item['date'])->locale('fr')->isoFormat('dddd D MMMM YYYY à HH:mm:ss') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
