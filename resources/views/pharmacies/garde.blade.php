@extends('layouts.master', ['title' => 'Pharmacies de Garde'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Pharmacies de Garde</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ url('index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste des Pharmacies de Garde</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="alert alert-info bg-info-100 text-info-600 border-info-100 px-24 py-11 mb-0 fw-semibold text-lg radius-8 d-flex align-items-center justify-content-between"
            role="alert">
            <div class="d-flex align-items-center gap-2">
                Dernière mise à jour : Du
                {{ \Carbon\Carbon::parse($premier['date_debut'])->locale('fr')->translatedFormat('l j F') }}
                au
                {{ \Carbon\Carbon::parse($premier['date_fin'])->locale('fr')->translatedFormat('l j F Y') }}
            </div>
        </div>

        <div class="card h-100 p-0 radius-12">
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3"></div>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <a href="{{ url('add-garde') }}"
                        class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
                        <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                        Ajouter Pharmacie de Garde
                    </a>
                    <a href="#"
                        class="btn btn-info text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#addExampleModal">
                        <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                        Mise à jour période de garde
                    </a>
                </div>
            </div>
            <div class="modal fade" id="addExampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                    <div class="modal-content radius-16 bg-base">
                        <div class="modal-header btn-info py-16 px-24 border border-top-0 border-start-0 border-end-0">
                            <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">Période de garde
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-24">
                            <form action="{{ url('add-garde') }}" method="post" role="form">
                                @csrf
                                <div class="alert alert-warning bg-warning-100 text-warning-600 border-warning-100 px-24 py-11 mb-0 fw-semibold text-lg radius-8 d-flex align-items-center justify-content-between"
                                    role="alert">
                                    <div class="d-flex align-items-center gap-2">
                                        Dernière mise à jour : Du
                                        {{ \Carbon\Carbon::parse($premier['date_debut'])->locale('fr')->translatedFormat('l j F') }}
                                        au
                                        {{ \Carbon\Carbon::parse($premier['date_fin'])->locale('fr')->translatedFormat('l j F Y') }}
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-6 mb-20">
                                        <label for="name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">Date
                                            début</label>
                                        <input type="date" name="debut" required class="form-control radius-8"
                                            id="name" placeholder="Saisir le libelle">
                                    </div>
                                    <div class="col-6 mb-20">
                                        <label for="name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">Date fin</label>
                                        <input type="date" name="fin" required class="form-control radius-8"
                                            id="name" placeholder="Saisir une petite description">
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                        <button type="reset" data-bs-dismiss="modal" aria-label="Close"
                                            class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                            Annuler
                                        </button>
                                        <button type="submit"
                                            class="btn btn-info border border-info-600 text-md px-50 py-12 radius-8">
                                            Modifier
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-24">
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                        <thead>
                            <tr>
                                <th scope="col" style="font-size: 13px">Commune</th>
                                <th scope="col" style="font-size: 13px">Nombre de pharmacie</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gardes ?? [] as $item)
                                <tr>
                                    <td style="font-size: 13px">{{ $item['communeName'] }}</td>
                                    <td style="font-size: 13px">{{ $item['nombreDePharmacie'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
