@extends('layouts.master', ['title' => 'Publicites'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Forfaits - {{ $libelle->libelle }}</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ url('index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste des forfaits</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="card h-100 p-0 radius-12">
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3">
                </div>
                <a href="#"
                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#addExampleModal">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Ajouter un forfait
                </a>
            </div>
            <div class="modal fade" id="addExampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                    <div class="modal-content radius-16 bg-base">
                        <div
                            class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0 bg-success text-white">
                            <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">
                                Ajout d'un forfait
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="color: white"></button>
                        </div>
                        <div class="modal-body p-24">
                            <form action="{{ url('add-forfait', $id) }}" method="post" role="form"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-20">
                                        <label for="libelle"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Libellé
                                        </label>
                                        <input type="text" name="libelle" required class="form-control radius-8"
                                            id="libelle" placeholder="Saisir un libellé">
                                    </div>
                                    <div class="col-md-6 mb-20">
                                        <label for="prix"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Prix
                                        </label>
                                        <input type="number" name="prix" required class="form-control radius-8"
                                            id="prix">
                                    </div>
                                    <div class="col-12 mb-20">
                                        <label for="description"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Description
                                        </label>
                                        <textarea name="description" required class="form-control radius-8" id="description" rows="3"
                                            placeholder="Entrer la description"></textarea>
                                    </div>
                                    <div class="col-md-6 mb-20">
                                        <label for="duration"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Durée (en jours)
                                        </label>
                                        <input type="number" name="duration" required class="form-control radius-8"
                                            id="duration">
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-center justify-content-end gap-3 mt-24">
                                            <button type="button" data-bs-dismiss="modal" class="btn btn-outline-danger">
                                                Annuler
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                Enregistrer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-24">
                <div class="row g-4">
                    @foreach ($publicites as $index => $item)
                        @php
                            // Palette de couleurs (tu peux ajouter d'autres)
$colors = ['#e3f2fd', '#e8f5e9', '#fff3e0', '#f3e5f5', '#ede7f6', '#fbe9e7'];
                            $bgColor = $colors[$index % count($colors)]; // Cycle des couleurs
                        @endphp

                        <div class="col-xl-4 col-md-6">
                            <div class="package-card h-100 shadow-sm border-0 radius-16 d-flex flex-column"
                                style="background: {{ $bgColor }};">
                                <div class="card-body p-24 d-flex flex-column flex-grow-1">

                                    {{-- Icône --}}
                                    <div class="icon-wrapper mb-20 text-center">
                                        <iconify-icon icon="solar:box-minimalistic-bold-duotone"
                                            class="package-icon"></iconify-icon>
                                    </div>

                                    {{-- Libellé --}}
                                    <h4 class="card-title text-primary-dark mb-12 text-center">{{ $item->libelle }}</h4>

                                    {{-- Prix et Durée --}}
                                    <div class="price-section mb-24 text-center">
                                        <span class="display-5 fw-bolder text-gradient">
                                            {{ number_format($item->price, 0, ',', ' ') }}
                                        </span>
                                        <small class="fs-6 text-muted">FCFA</small>
                                        <p class="text-muted mt-1">/ {{ $item->duration }} jours</p>
                                    </div>

                                    {{-- Description --}}
                                    <p class="card-text text-secondary-light mb-32 flex-grow-1">
                                        {!! $item->description !!}
                                    </p>

                                    {{-- Bouton --}}
                                    <div class="mt-auto">
                                        <a href="javascript:void(0)" class="btn btn-primary w-100 animate-btn"
                                            data-bs-toggle="modal" data-bs-target="#edit{{ $item->id_service }}">
                                            Modifier le forfait
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="edit{{ $item->id_service }}" tabindex="-1"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content radius-16 bg-base">
                                        <div class="modal-header py-16 px-24 border-0 bg-secondary">
                                            <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">
                                                Modification du forfait
                                            </h1>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-24">
                                            <form action="{{ route('pricing.update', $item->id_service) }}" method="post"
                                                role="form" enctype="multipart/form-data">
                                                @csrf
                                                @method('PATCH')
                                                <div class="row">
                                                    <div class="col-md-6 mb-20">
                                                        <label for="libelle{{ $item->id_service }}"
                                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                            Libellé
                                                        </label>
                                                        <input value="{{ $item->libelle }}" type="text"
                                                            name="libelle" required class="form-control radius-8"
                                                            id="libelle{{ $item->id_service }}"
                                                            placeholder="Saisir un libellé">
                                                    </div>
                                                    <div class="col-md-6 mb-20">
                                                        <label for="prix{{ $item->id_service }}"
                                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                            Prix
                                                        </label>
                                                        <input value="{{ $item->price }}" type="number" name="prix"
                                                            required class="form-control radius-8"
                                                            id="prix{{ $item->id_service }}">
                                                    </div>
                                                    <div class="col-12 mb-20">
                                                        <label for="description{{ $item->id_service }}"
                                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                            Description
                                                        </label>
                                                        <textarea name="description" required class="form-control radius-8" id="description{{ $item->id_service }}"
                                                            rows="3" placeholder="Entrer la description">{{ $item->description }}</textarea>
                                                    </div>
                                                    <div class="col-md-6 mb-20">
                                                        <label for="duration{{ $item->id_service }}"
                                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                            Durée (en jours)
                                                        </label>
                                                        <input value="{{ $item->duration }}" type="number"
                                                            name="duration" required class="form-control radius-8"
                                                            id="duration{{ $item->id_service }}">
                                                    </div>
                                                    <div class="col-12">
                                                        <div
                                                            class="d-flex align-items-center justify-content-end gap-3 mt-24">
                                                            <button type="button" data-bs-dismiss="modal"
                                                                class="btn btn-outline-danger">
                                                                Annuler
                                                            </button>
                                                            <button type="submit" class="btn btn-secondary">
                                                                Enregistrer les modifications
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <style>
                /* Styles pour les cartes de forfait */
                .package-card {
                    background: #ffffff;
                    /* Fond blanc par défaut */
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    /* Transition pour l'animation */
                    border: 1px solid #e0e0e0;
                    /* Bordure subtile */
                    overflow: hidden;
                    /* Pour s'assurer que l'ombre ne déborde pas de manière inesthétique */
                }

                .package-card:hover {
                    transform: translateY(-8px);
                    /* Élève la carte de 8px */
                    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
                    /* Ombre plus prononcée */
                }

                /* Style de l'icône du forfait */
                .package-icon {
                    font-size: 3.5rem;
                    /* Grande taille d'icône */
                    color: var(--bs-primary);
                    /* Utilise la couleur primaire de Bootstrap */
                    transition: color 0.3s ease;
                }

                .package-card:hover .package-icon {
                    color: var(--bs-secondary);
                    /* Change de couleur au survol pour un effet */
                }

                /* Style pour le texte dégradé du prix */
                .text-gradient {
                    /* Cela nécessite un préfixe pour la compatibilité navigateur, mais voici la base */
                    background: linear-gradient(45deg, var(--bs-primary), var(--bs-info));
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                    color: transparent;
                    /* Fallback pour les navigateurs non compatibles */
                    font-weight: 900;
                    /* Extra bold */
                }

                /* Animation subtile pour le bouton au survol */
                .animate-btn {
                    position: relative;
                    overflow: hidden;
                    z-index: 1;
                    transition: color 0.3s ease-in-out, background-color 0.3s ease-in-out;
                }

                .animate-btn::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: rgba(255, 255, 255, 0.1);
                    /* Effet de lumière */
                    transition: left 0.5s ease-in-out;
                    z-index: -1;
                }

                .animate-btn:hover::before {
                    left: 0;
                }

                /* Style pour le modal (peut être animé avec Bootstrap lui-même, mais voici un exemple de transition personnalisée si besoin) */
                .modal.fade .modal-dialog {
                    transition: transform .3s ease-out;
                    transform: translateY(-50px);
                    /* Commence un peu au-dessus */
                }

                .modal.show .modal-dialog {
                    transform: translateY(0);
                    /* Descend en position */
                }
            </style>
        </div>
    </div>
@endsection
