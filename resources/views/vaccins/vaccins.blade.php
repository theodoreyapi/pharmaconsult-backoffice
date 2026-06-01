@extends('layouts.master', ['title' => 'Vaccins'])

@section('content')

    <div class="dashboard-main-body">

        {{-- ── Fil d'Ariane ─────────────────────────────────────────────────────── --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Gestion des vaccins</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Vaccins</li>
            </ul>
        </div>

        {{-- ── Alertes ──────────────────────────────────────────────────────────── --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <iconify-icon icon="solar:check-circle-bold" class="me-2"></iconify-icon>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <iconify-icon icon="solar:close-circle-bold" class="me-2"></iconify-icon>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ── Stats rapides ────────────────────────────────────────────────────── --}}
        <div class="row row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4 mb-24">

            <div class="col">
                <div class="card shadow-none border h-100" style="border-left:4px solid #2E7D32!important">
                    <div class="card-body p-20 d-flex align-items-center gap-3">
                        <div class="w-48-px h-48-px rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"
                            style="background:#2E7D32">
                            <iconify-icon icon="solar:syringe-bold" class="text-white text-xl"></iconify-icon>
                        </div>
                        <div>
                            <p class="text-secondary-light fw-medium mb-1 text-sm">Total vaccins</p>
                            <h5 class="fw-bold mb-0">{{ $stats['total'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card shadow-none border h-100" style="border-left:4px solid #16A34A!important">
                    <div class="card-body p-20 d-flex align-items-center gap-3">
                        <div class="w-48-px h-48-px rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"
                            style="background:#16A34A">
                            <iconify-icon icon="solar:shield-check-bold" class="text-white text-xl"></iconify-icon>
                        </div>
                        <div>
                            <p class="text-secondary-light fw-medium mb-1 text-sm">Actifs</p>
                            <h5 class="fw-bold mb-0">{{ $stats['active'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card shadow-none border h-100" style="border-left:4px solid #1565C0!important">
                    <div class="card-body p-20 d-flex align-items-center gap-3">
                        <div class="w-48-px h-48-px rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"
                            style="background:#1565C0">
                            <iconify-icon icon="solar:person-bold" class="text-white text-xl"></iconify-icon>
                        </div>
                        <div>
                            <p class="text-secondary-light fw-medium mb-1 text-sm">Humains</p>
                            <h5 class="fw-bold mb-0">{{ $stats['human'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card shadow-none border h-100" style="border-left:4px solid #E65100!important">
                    <div class="card-body p-20 d-flex align-items-center gap-3">
                        <div class="w-48-px h-48-px rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"
                            style="background:#E65100">
                            <iconify-icon icon="solar:pet-bold" class="text-white text-xl"></iconify-icon>
                        </div>
                        <div>
                            <p class="text-secondary-light fw-medium mb-1 text-sm">Animaux</p>
                            <h5 class="fw-bold mb-0">{{ $stats['animal'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Card principale ──────────────────────────────────────────────────── --}}
        <div class="card shadow-none border">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 py-16 px-24">
                <h6 class="fw-semibold mb-0">Liste des vaccins</h6>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    {{-- Filtres --}}
                    <form method="GET" action="{{ route('vaccins.index') }}"
                        class="d-flex flex-wrap align-items-center gap-2">

                        <div class="icon-field">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control form-control-sm" style="min-width:180px" placeholder="Nom, slug…">
                            <span class="icon"><iconify-icon icon="ion:search-outline"></iconify-icon></span>
                        </div>

                        <select name="type" class="form-select form-select-sm w-auto">
                            <option value="">Tous les types</option>
                            <option value="human" {{ request('type') === 'human' ? 'selected' : '' }}>Humain</option>
                            <option value="animal" {{ request('type') === 'animal' ? 'selected' : '' }}>Animal</option>
                        </select>

                        <select name="category" class="form-select form-select-sm w-auto">
                            <option value="">Toutes catégories</option>
                            @foreach ($allCategories as $cat)
                                <option value="{{ $cat->id_categorie }}"
                                    {{ request('category') == $cat->id_categorie ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status" class="form-select form-select-sm w-auto">
                            <option value="">Tous statuts</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactif</option>
                        </select>

                        <button type="submit" class="btn btn-sm btn-outline-secondary">Filtrer</button>
                        @if (request('search') || request('type') || request('category') || request('status') !== null)
                            <a href="{{ route('vaccins.index') }}" class="btn btn-sm btn-outline-danger">×</a>
                        @endif
                    </form>

                    <button type="button" class="btn btn-sm text-white" style="background:#2E7D32" data-bs-toggle="modal"
                        data-bs-target="#modalCreate">
                        {{-- <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> --}}
                        Nouveau vaccin
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Prix public</th>
                                <th>Prix privé</th>
                                <th>Catégories</th>
                                <th class="text-center">Statut</th>
                                <th>Créé le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vaccines as $vaccine)
                                <tr>
                                    <td class="text-secondary-light">{{ $vaccine->id_vaccine }}</td>

                                    <td>
                                        <div>
                                            <span
                                                class="fw-semibold text-primary-light d-block">{{ $vaccine->name }}</span>
                                            @if ($vaccine->short_name)
                                                <code
                                                    class="text-xs text-secondary-light">{{ $vaccine->short_name }}</code>
                                            @endif
                                            @if ($vaccine->description)
                                                <p class="text-xs text-secondary-light mb-0 mt-1"
                                                    style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                                    {{ $vaccine->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        @if ($vaccine->vaccine_type === 'human')
                                            <span class="badge px-10 py-5 fw-semibold"
                                                style="background:rgba(21,101,192,.1);color:#1565C0;border-radius:20px">
                                                <iconify-icon icon="solar:person-bold" class="me-1"></iconify-icon>
                                                Humain
                                            </span>
                                        @else
                                            <span class="badge px-10 py-5 fw-semibold"
                                                style="background:rgba(230,81,0,.1);color:#E65100;border-radius:20px">
                                                <iconify-icon icon="solar:pet-bold" class="me-1"></iconify-icon>
                                                Animal
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($vaccine->public_price == 0)
                                            <span class="text-success-main fw-bold">GRATUIT</span>
                                        @else
                                            <span class="fw-semibold">
                                                {{ number_format($vaccine->public_price, 0, ',', ' ') }}
                                                {{ $vaccine->currency }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-secondary-light text-sm">
                                        @if ($vaccine->private_price_min || $vaccine->private_price_max)
                                            {{ number_format($vaccine->private_price_min, 0, ',', ' ') }}
                                            –
                                            {{ number_format($vaccine->private_price_max, 0, ',', ' ') }}
                                            {{ $vaccine->currency }}
                                        @else
                                            <span class="text-secondary-light">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @forelse($vaccine->categories as $cat)
                                                <span class="badge px-8 py-4 text-xs fw-medium"
                                                    style="background:rgba(46,125,50,.1);color:#2E7D32;border-radius:20px">
                                                    {{ $cat->name }}
                                                </span>
                                            @empty
                                                <span class="text-secondary-light text-xs">—</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        @if ($vaccine->is_active)
                                            <span
                                                class="badge bg-success-focus text-success-main px-12 py-4 rounded-pill fw-semibold text-sm">
                                                Actif
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-danger-focus text-danger-main px-12 py-4 rounded-pill fw-semibold text-sm">
                                                Inactif
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-secondary-light text-sm">
                                        {{ \Carbon\Carbon::parse($vaccine->created_at)->format('d M Y') }}
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            {{-- Voir --}}
                                            <button
                                                class="w-32-px h-32-px d-flex justify-content-center align-items-center rounded-circle"
                                                style="background:rgba(46,125,50,.1);color:#2E7D32" title="Détail"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalView{{ $vaccine->id_vaccine }}">
                                                <iconify-icon icon="solar:eye-bold"></iconify-icon>
                                            </button>
                                            {{-- Modifier --}}
                                            <button
                                                class="w-32-px h-32-px d-flex justify-content-center align-items-center rounded-circle bg-primary-focus text-primary-600 btn-edit"
                                                title="Modifier" data-vaccine="{{ json_encode($vaccine) }}"
                                                data-categories="{{ json_encode($vaccine->categories->pluck('id_categorie')) }}"
                                                data-bs-toggle="modal" data-bs-target="#modalEdit">
                                                <iconify-icon icon="lucide:edit"></iconify-icon>
                                            </button>
                                            {{-- Toggle statut --}}
                                            <form action="{{ route('vaccins.toggle', $vaccine->id_vaccine) }}"
                                                method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="w-32-px h-32-px d-flex justify-content-center align-items-center rounded-circle"
                                                    style="background:{{ $vaccine->is_active ? 'rgba(220,38,38,.1)' : 'rgba(22,163,74,.1)' }};
                                                       color:{{ $vaccine->is_active ? '#DC2626' : '#16A34A' }}"
                                                    title="{{ $vaccine->is_active ? 'Désactiver' : 'Activer' }}">
                                                    <iconify-icon
                                                        icon="{{ $vaccine->is_active ? 'solar:pause-bold' : 'solar:play-bold' }}"></iconify-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Modal détail --}}
                                <div class="modal fade" id="modalView{{ $vaccine->id_vaccine }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                                        <div class="modal-content border-0 radius-12 overflow-hidden">

                                            {{-- HEADER --}}
                                            <div class="modal-header px-24 py-20 border-0"
                                                style="background:linear-gradient(135deg,#0f172a,#1e293b)">

                                                <div>
                                                    <h5 class="fw-bold text-white mb-1 d-flex align-items-center gap-2">
                                                        <iconify-icon icon="solar:syringe-bold"></iconify-icon>
                                                        {{ $vaccine->name }}
                                                    </h5>

                                                    <div class="d-flex flex-wrap gap-2 mt-2">

                                                        @if ($vaccine->vaccine_type == 'human')
                                                            <span class="badge bg-info">
                                                                👤 Humain
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">
                                                                🐾 Animal
                                                            </span>
                                                        @endif

                                                        @if ($vaccine->is_active)
                                                            <span class="badge bg-success">
                                                                Actif
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                Inactif
                                                            </span>
                                                        @endif

                                                        <span class="badge bg-primary">
                                                            {{ $vaccine->validation_status }}
                                                        </span>

                                                    </div>
                                                </div>

                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>

                                            {{-- BODY --}}
                                            <div class="modal-body p-24">

                                                <div class="row gy-4">

                                                    {{-- INFOS --}}
                                                    <div class="col-lg-8">

                                                        <div class="card border radius-10 h-100">
                                                            <div class="card-body">

                                                                <h6 class="fw-bold mb-20">
                                                                    Informations générales
                                                                </h6>

                                                                <div class="row gy-3">

                                                                    <div class="col-md-6">
                                                                        <small class="text-secondary">
                                                                            Nom court
                                                                        </small>

                                                                        <div class="fw-semibold">
                                                                            {{ $vaccine->short_name ?: '—' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <small class="text-secondary">
                                                                            Slug
                                                                        </small>

                                                                        <div>
                                                                            <code>{{ $vaccine->slug }}</code>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <small class="text-secondary">
                                                                            Maladies ciblées
                                                                        </small>

                                                                        <div class="fw-semibold">
                                                                            {{ $vaccine->targeted_disease ?: '—' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <small class="text-secondary">
                                                                            Administration
                                                                        </small>

                                                                        <div class="fw-semibold">
                                                                            {{ $vaccine->administration_mode ?: '—' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <small class="text-secondary">
                                                                            Type scientifique
                                                                        </small>

                                                                        <div class="fw-semibold">
                                                                            {{ $vaccine->scientific_type ?: '—' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <small class="text-secondary">
                                                                            Espèce cible
                                                                        </small>

                                                                        <div class="fw-semibold">
                                                                            {{ $vaccine->target_species ?: '—' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <small class="text-secondary">
                                                                            Description
                                                                        </small>

                                                                        <div class="mt-1">
                                                                            {{ $vaccine->description ?: 'Aucune description' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <small class="text-secondary">
                                                                            Protège contre
                                                                        </small>

                                                                        <div class="mt-1">
                                                                            {{ $vaccine->protected_against ?: '—' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <small class="text-secondary">
                                                                            Public concerné
                                                                        </small>

                                                                        <div class="mt-1">
                                                                            {{ $vaccine->target_public ?: '—' }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <small class="text-secondary">
                                                                            Informations importantes
                                                                        </small>

                                                                        <div class="alert alert-info mt-2 mb-0">
                                                                            {{ $vaccine->important_info ?: '—' }}
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                            </div>
                                                        </div>

                                                    </div>

                                                    {{-- TARIFS + CATEGORIES --}}
                                                    <div class="col-lg-4">

                                                        {{-- PRIX --}}
                                                        <div class="card border radius-10 mb-4">
                                                            <div class="card-body">

                                                                <h6 class="fw-bold mb-20">
                                                                    Tarification
                                                                </h6>

                                                                <div class="mb-3">
                                                                    <small class="text-secondary">
                                                                        Prix public
                                                                    </small>

                                                                    <div class="fw-bold text-success fs-5">

                                                                        @if ($vaccine->public_price == 0)
                                                                            GRATUIT
                                                                        @else
                                                                            {{ number_format($vaccine->public_price, 0, ',', ' ') }}
                                                                            {{ $vaccine->currency }}
                                                                        @endif

                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <small class="text-secondary">
                                                                        Prix privé
                                                                    </small>

                                                                    <div class="fw-semibold">

                                                                        {{ $vaccine->private_price_min ?: 0 }}
                                                                        -
                                                                        {{ $vaccine->private_price_max ?: 0 }}

                                                                        {{ $vaccine->currency }}

                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>

                                                        {{-- CATEGORIES --}}
                                                        <div class="card border radius-10">
                                                            <div class="card-body">

                                                                <h6 class="fw-bold mb-20">
                                                                    Catégories
                                                                </h6>

                                                                <div class="d-flex flex-wrap gap-2">

                                                                    @forelse($vaccine->categories as $cat)
                                                                        <span class="badge bg-success-subtle text-success">

                                                                            {{ $cat->name }}

                                                                        </span>

                                                                    @empty

                                                                        <span class="text-secondary">
                                                                            Aucune catégorie
                                                                        </span>
                                                                    @endforelse

                                                                </div>

                                                            </div>
                                                        </div>

                                                    </div>

                                                    {{-- CALENDRIER --}}
                                                    @if ($vaccine->schedule)
                                                        <div class="col-12">

                                                            <div class="card border radius-10">
                                                                <div class="card-body">

                                                                    <h6 class="fw-bold mb-20">
                                                                        Calendrier vaccinal
                                                                    </h6>

                                                                    <div class="row gy-3">

                                                                        <div class="col-md-3">
                                                                            <small class="text-secondary">
                                                                                Phase
                                                                            </small>

                                                                            <div class="fw-semibold">
                                                                                {{ $vaccine->schedule->phase_name ?: '—' }}
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-2">
                                                                            <small class="text-secondary">
                                                                                Dose
                                                                            </small>

                                                                            <div class="fw-semibold">
                                                                                {{ $vaccine->schedule->dose_number ?: '—' }}
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-3">
                                                                            <small class="text-secondary">
                                                                                Âge
                                                                            </small>

                                                                            <div class="fw-semibold">
                                                                                {{ $vaccine->schedule->age_label ?: '—' }}
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-2">
                                                                            <small class="text-secondary">
                                                                                Sexe
                                                                            </small>

                                                                            <div class="fw-semibold">
                                                                                {{ $vaccine->schedule->gender ?: 'Tous' }}
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-2">
                                                                            <small class="text-secondary">
                                                                                Priorité
                                                                            </small>

                                                                            <div class="fw-semibold">
                                                                                {{ $vaccine->schedule->priority ?: 0 }}
                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </div>

                                                        </div>
                                                    @endif

                                                    {{-- RESTRICTIONS --}}
                                                    <div class="col-12">

                                                        <div class="card border radius-10">
                                                            <div class="card-body">

                                                                <h6 class="fw-bold mb-20 text-danger">
                                                                    Restrictions / contre indications
                                                                </h6>

                                                                <div class="d-flex flex-wrap gap-2 mb-3">

                                                                    @forelse($vaccine->restrictions as $r)
                                                                        <span class="badge bg-danger-subtle text-danger">

                                                                            {{ $r->restriction_type }}

                                                                        </span>

                                                                    @empty

                                                                        <span class="text-secondary">
                                                                            Aucune restriction
                                                                        </span>
                                                                    @endforelse

                                                                </div>

                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                            {{-- FOOTER --}}
                                            <div class="modal-footer border-0 px-24 pb-24">

                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                                                    Fermer

                                                </button>

                                            </div>

                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-32 text-secondary-light">
                                        <iconify-icon icon="solar:syringe-broken"
                                            class="text-4xl mb-2 d-block"></iconify-icon>
                                        Aucun vaccin trouvé.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if ($vaccines->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center py-12 px-24">
                    <p class="text-secondary-light text-sm mb-0">
                        {{ $vaccines->firstItem() }}–{{ $vaccines->lastItem() }} sur {{ $vaccines->total() }} vaccins
                    </p>
                    {{ $vaccines->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

    </div>{{-- /dashboard-main-body --}}

    <style>
        #modalCreate .modal-body,
        #modalEdit .modal-body {
            max-height: calc(100vh - 180px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        #modalCreate .modal-content,
        #modalEdit .modal-content {
            max-height: 95vh;
            overflow: hidden;
        }
    </style>

    {{-- ══════════════════════════════════════════════════════════════════════════
     MODAL — Créer un vaccin
     Tous les champs : vaccin + schedule + restrictions + catégories
══════════════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalCreate" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content radius-12">

                {{-- En-tête --}}
                <div class="modal-header border-bottom py-16 px-24"
                    style="background:linear-gradient(135deg,#f0fdf4,#dcfce7)">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <span class="w-32-px h-32-px rounded-circle d-flex align-items-center justify-content-center"
                            style="background:#2E7D32">
                            <iconify-icon icon="solar:add-circle-bold" class="text-white"></iconify-icon>
                        </span>
                        Nouveau vaccin
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('vaccins.store') }}" method="POST">
                    @csrf
                    <div class="modal-body py-24 px-24">

                        {{-- ── Section 1 : Informations générales ──────────────────── --}}
                        <div class="section-block mb-24">
                            <div class="d-flex align-items-center gap-2 mb-16">
                                <span class="badge rounded-pill text-white fw-semibold px-12 py-6"
                                    style="background:#2E7D32;font-size:.7rem">01</span>
                                <h6 class="fw-bold mb-0 text-sm">Informations générales</h6>
                                <hr class="flex-fill my-0 ms-2">
                            </div>

                            <div class="row gy-16">
                                <div class="col-sm-7">
                                    <label class="form-label fw-semibold text-sm mb-8">
                                        Nom complet <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="createName" class="form-control"
                                        placeholder="Ex : Vaccin Anti-Tétanique" required>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Nom court / Sigle</label>
                                    <input type="text" name="short_name" class="form-control" placeholder="Ex : VAT">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label fw-semibold text-sm mb-8">Type <span
                                            class="text-danger">*</span></label>
                                    <select name="vaccine_type" id="createType" class="form-select" required>
                                        <option value="human">👤 Humain</option>
                                        <option value="animal">🐾 Animal</option>
                                    </select>
                                </div>

                                <div class="col-sm-5">
                                    <label class="form-label fw-semibold text-sm mb-8">
                                        Slug
                                        <span class="text-secondary-light text-xs fw-normal">(généré
                                            automatiquement)</span>
                                    </label>
                                    <input type="text" name="slug" id="createSlug" class="form-control"
                                        placeholder="vat-vaccin-anti-tetanique">
                                </div>
                                <div class="col-sm-4" id="wrapSpeciesCreate" style="display:none">
                                    <label class="form-label fw-semibold text-sm mb-8">Espèce cible</label>
                                    <input type="text" name="target_species" class="form-control"
                                        placeholder="Ex : Chien, Chat, Cheval">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Statut validation</label>
                                    <select name="validation_status" class="form-select">
                                        <option value="pending">⏳ En attente</option>
                                        <option value="validated_dsv">✅ Validé DSV</option>
                                        <option value="validated_inhp">✅ Validé INHP</option>
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold text-sm mb-8">Maladie(s) ciblée(s)</label>
                                    <input type="text" name="targeted_disease" class="form-control"
                                        placeholder="Ex : Tétanos, Diphtérie">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Mode d'administration</label>
                                    <input type="text" name="administration_mode" class="form-control"
                                        placeholder="Ex : Sous-cutanée">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Type scientifique</label>
                                    <input type="text" name="scientific_type" class="form-control"
                                        placeholder="Ex : Vivant atténué">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-sm mb-8">Description</label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="Description générale du vaccin…"></textarea>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold text-sm mb-8">Protège contre</label>
                                    <textarea name="protected_against" class="form-control" rows="2"
                                        placeholder="Ex : Protège contre le tétanos, la diphtérie…"></textarea>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold text-sm mb-8">Public concerné</label>
                                    <textarea name="target_public" class="form-control" rows="2"
                                        placeholder="Ex : Enfants de 0 à 5 ans, femmes enceintes…"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-sm mb-8">Informations importantes</label>
                                    <textarea name="important_info" class="form-control" rows="2"
                                        placeholder="Ex : Série de 3 doses nécessaires, contre-indications majeures…"></textarea>
                                </div>
                                <div class="col-sm-8">
                                    <label class="form-label fw-semibold text-sm mb-8">URL source / référence</label>
                                    <input type="url" name="source_url" class="form-control"
                                        placeholder="https://…">
                                </div>
                                <div class="col-sm-4 d-flex align-items-end pb-2">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="createActive" checked>
                                        <label class="form-check-label fw-semibold text-sm" for="createActive">
                                            Vaccin actif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 2 : Tarification ───────────────────────────── --}}
                        <div class="section-block mb-24">
                            <div class="d-flex align-items-center gap-2 mb-16">
                                <span class="badge rounded-pill text-white fw-semibold px-12 py-6"
                                    style="background:#1565C0;font-size:.7rem">02</span>
                                <h6 class="fw-bold mb-0 text-sm">Tarification</h6>
                                <hr class="flex-fill my-0 ms-2">
                            </div>

                            <div class="row gy-16">
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold text-sm mb-8">
                                        Prix public (FCFA)
                                        <span class="text-secondary-light text-xs fw-normal">0 = GRATUIT</span>
                                    </label>
                                    <input type="number" name="public_price" class="form-control" value="0"
                                        min="0" step="1" placeholder="0">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold text-sm mb-8">Prix privé min (FCFA)</label>
                                    <input type="number" name="private_price_min" class="form-control" min="0"
                                        step="1" placeholder="Ex : 2 000">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold text-sm mb-8">Prix privé max (FCFA)</label>
                                    <input type="number" name="private_price_max" class="form-control" min="0"
                                        step="1" placeholder="Ex : 3 500">
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 3 : Calendrier vaccinal ────────────────────── --}}
                        <div class="section-block mb-24">
                            <div class="d-flex align-items-center gap-2 mb-16">
                                <span class="badge rounded-pill text-white fw-semibold px-12 py-6"
                                    style="background:#0891B2;font-size:.7rem">03</span>
                                <h6 class="fw-bold mb-0 text-sm">Calendrier vaccinal</h6>
                                <hr class="flex-fill my-0 ms-2">
                            </div>

                            <div class="row gy-16">
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Phase / Nom de la dose</label>
                                    <input type="text" name="schedule[phase_name]" class="form-control"
                                        placeholder="Ex : Dose 1, Primovaccination">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label fw-semibold text-sm mb-8">N° de dose</label>
                                    <input type="number" name="schedule[dose_number]" class="form-control"
                                        min="1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Libellé âge</label>
                                    <input type="text" name="schedule[age_label]" class="form-control"
                                        placeholder="Ex : Dès la naissance">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label fw-semibold text-sm mb-8">Âge min (mois)</label>
                                    <input type="number" name="schedule[min_age_months]" class="form-control"
                                        min="0" step="0.5" value="0">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label fw-semibold text-sm mb-8">Âge max (mois)</label>
                                    <input type="number" name="schedule[max_age_months]" class="form-control"
                                        min="0" step="0.5">
                                </div>

                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Sexe concerné</label>
                                    <select name="schedule[gender]" class="form-select">
                                        <option value="all">Tous</option>
                                        <option value="masculin">Masculin</option>
                                        <option value="feminin">Féminin</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label fw-semibold text-sm mb-8">Priorité</label>
                                    <input type="number" name="schedule[priority]" class="form-control" value="0">
                                </div>
                                <div class="col-sm-4 d-flex align-items-end pb-2">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="schedule[is_booster]"
                                            id="createIsBooster" value="1">
                                        <label class="form-check-label text-sm" for="createIsBooster">
                                            Vaccin de rappel
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Rappel tous les (mois)</label>
                                    <input type="number" name="schedule[booster_every_months]" class="form-control"
                                        min="1" placeholder="Ex : 12">
                                </div>

                                {{-- Contextes --}}
                                <div class="col-12">
                                    <p class="form-label fw-semibold text-sm mb-8">Contextes spéciaux</p>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="schedule[only_pregnant]" value="1" id="c_pregnant">
                                            <label class="form-check-label text-sm" for="c_pregnant">Femmes enceintes
                                                uniquement</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="schedule[for_travelers]" value="1" id="c_travelers">
                                            <label class="form-check-label text-sm" for="c_travelers">Voyageurs</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="schedule[in_community]"
                                                value="1" id="c_community">
                                            <label class="form-check-label text-sm" for="c_community">Milieu
                                                collectif</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="schedule[for_health_workers]" value="1" id="c_health">
                                            <label class="form-check-label text-sm" for="c_health">Professionnels de
                                                santé</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="schedule[for_immunocompromised]" value="1" id="c_immuno">
                                            <label class="form-check-label text-sm" for="c_immuno">Immunodéprimés</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="schedule[for_seniors]"
                                                value="1" id="c_seniors">
                                            <label class="form-check-label text-sm" for="c_seniors">Personnes
                                                âgées</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="schedule[exposed_to_vectors]" value="1" id="c_vectors">
                                            <label class="form-check-label text-sm" for="c_vectors">Exposés aux
                                                vecteurs</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6" id="wrapTravelZoneCreate" style="display:none">
                                    <label class="form-label fw-semibold text-sm mb-8">Zone de voyage</label>
                                    <input type="text" name="schedule[travel_zone]" class="form-control"
                                        placeholder="Ex : Afrique subsaharienne, Zone CEDEAO">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-sm mb-8">Note importante (calendrier)</label>
                                    <textarea name="schedule[important_note]" class="form-control" rows="2"
                                        placeholder="Ex : À administrer en 3 injections espacées de 4 semaines…"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 4 : Restrictions ────────────────────────────── --}}
                        <div class="section-block mb-24">
                            <div class="d-flex align-items-center gap-2 mb-16">
                                <span class="badge rounded-pill text-white fw-semibold px-12 py-6"
                                    style="background:#DC2626;font-size:.7rem">04</span>
                                <h6 class="fw-bold mb-0 text-sm">Restrictions / Contre-indications</h6>
                                <hr class="flex-fill my-0 ms-2">
                            </div>

                            <div class="row gy-16">
                                <div class="col-12">
                                    <div class="d-flex flex-wrap gap-3 p-12 rounded-8"
                                        style="background:rgba(220,38,38,.04);border:1px solid rgba(220,38,38,.15)">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="restrictions[]"
                                                value="pregnancy" id="c_restr_preg">
                                            <label class="form-check-label text-sm" for="c_restr_preg">
                                                🤰 Grossesse
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="restrictions[]"
                                                value="immunocompromised" id="c_restr_immuno">
                                            <label class="form-check-label text-sm" for="c_restr_immuno">
                                                🛡️ Immunodéprimés
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="restrictions[]"
                                                value="allergy" id="c_restr_allergy">
                                            <label class="form-check-label text-sm" for="c_restr_allergy">
                                                ⚠️ Allergies
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-sm mb-8">Motif / explication</label>
                                    <textarea name="restriction_reason" class="form-control" rows="2"
                                        placeholder="Ex : Contre-indiqué pendant le premier trimestre de grossesse…"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 5 : Catégories ──────────────────────────────── --}}
                        <div class="section-block">
                            <div class="d-flex align-items-center gap-2 mb-16">
                                <span class="badge rounded-pill text-white fw-semibold px-12 py-6"
                                    style="background:#7C3AED;font-size:.7rem">05</span>
                                <h6 class="fw-bold mb-0 text-sm">Catégories</h6>
                                <hr class="flex-fill my-0 ms-2">
                            </div>
                            <div class="d-flex flex-wrap gap-2 p-12 rounded-8"
                                style="border:1px solid #e0e0e0;min-height:48px">
                                @foreach ($allCategories as $cat)
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="checkbox" name="categories[]"
                                            value="{{ $cat->id_categorie }}" id="cat_c_{{ $cat->id_categorie }}">
                                        <label class="form-check-label text-sm" for="cat_c_{{ $cat->id_categorie }}">
                                            {{ $cat->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>{{-- /modal-body --}}

                    <div class="modal-footer border-top px-24" style="padding: inherit;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn text-white px-24" style="background:#2E7D32">
                            {{-- <iconify-icon icon="solar:diskette-bold" class="me-1"></iconify-icon> --}}
                            Enregistrer le vaccin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════
     MODAL — Modifier un vaccin
     Pré-remplissage complet via JS (vaccine + schedule + restrictions)
══════════════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content radius-12">

                <div class="modal-header border-bottom py-16 px-24"
                    style="background:linear-gradient(135deg,#eff6ff,#dbeafe)">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <span class="w-32-px h-32-px rounded-circle d-flex align-items-center justify-content-center"
                            style="background:#1565C0">
                            <iconify-icon icon="lucide:edit" class="text-white"></iconify-icon>
                        </span>
                        Modifier le vaccin
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="formEdit" action="" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body py-24 px-24">

                        {{-- ── Section 1 : Informations générales ──────────────────── --}}
                        <div class="section-block mb-24">
                            <div class="d-flex align-items-center gap-2 mb-16">
                                <span class="badge rounded-pill text-white fw-semibold px-12 py-6"
                                    style="background:#2E7D32;font-size:.7rem">01</span>
                                <h6 class="fw-bold mb-0 text-sm">Informations générales</h6>
                                <hr class="flex-fill my-0 ms-2">
                            </div>

                            <div class="row gy-16">
                                <div class="col-sm-7">
                                    <label class="form-label fw-semibold text-sm mb-8">
                                        Nom complet <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="editName" class="form-control" required>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Nom court / Sigle</label>
                                    <input type="text" name="short_name" id="editShortName" class="form-control">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label fw-semibold text-sm mb-8">Type <span
                                            class="text-danger">*</span></label>
                                    <select name="vaccine_type" id="editType" class="form-select" required>
                                        <option value="human">👤 Humain</option>
                                        <option value="animal">🐾 Animal</option>
                                    </select>
                                </div>

                                <div class="col-sm-5">
                                    <label class="form-label fw-semibold text-sm mb-8">Slug</label>
                                    <input type="text" name="slug" id="editSlug" class="form-control">
                                </div>
                                <div class="col-sm-4" id="wrapSpeciesEdit" style="display:none">
                                    <label class="form-label fw-semibold text-sm mb-8">Espèce cible</label>
                                    <input type="text" name="target_species" id="editTargetSpecies"
                                        class="form-control" placeholder="Ex : Chien, Chat, Cheval">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Statut validation</label>
                                    <select name="validation_status" id="editValidationStatus" class="form-select">
                                        <option value="pending">⏳ En attente</option>
                                        <option value="validated_dsv">✅ Validé DSV</option>
                                        <option value="validated_inhp">✅ Validé INHP</option>
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold text-sm mb-8">Maladie(s) ciblée(s)</label>
                                    <input type="text" name="targeted_disease" id="editTargetedDisease"
                                        class="form-control">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Mode d'administration</label>
                                    <input type="text" name="administration_mode" id="editAdminMode"
                                        class="form-control">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Type scientifique</label>
                                    <input type="text" name="scientific_type" id="editScientificType"
                                        class="form-control">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-sm mb-8">Description</label>
                                    <textarea name="description" id="editDescription" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold text-sm mb-8">Protège contre</label>
                                    <textarea name="protected_against" id="editProtectedAgainst" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold text-sm mb-8">Public concerné</label>
                                    <textarea name="target_public" id="editTargetPublic" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-sm mb-8">Informations importantes</label>
                                    <textarea name="important_info" id="editImportantInfo" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-sm-8">
                                    <label class="form-label fw-semibold text-sm mb-8">URL source / référence</label>
                                    <input type="url" name="source_url" id="editSourceUrl" class="form-control">
                                </div>
                                <div class="col-sm-4 d-flex align-items-end pb-2">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="editActive">
                                        <label class="form-check-label fw-semibold text-sm" for="editActive">
                                            Vaccin actif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 2 : Tarification ───────────────────────────── --}}
                        <div class="section-block mb-24">
                            <div class="d-flex align-items-center gap-2 mb-16">
                                <span class="badge rounded-pill text-white fw-semibold px-12 py-6"
                                    style="background:#1565C0;font-size:.7rem">02</span>
                                <h6 class="fw-bold mb-0 text-sm">Tarification</h6>
                                <hr class="flex-fill my-0 ms-2">
                            </div>
                            <div class="row gy-16">
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold text-sm mb-8">
                                        Prix public (FCFA)
                                        <span class="text-secondary-light text-xs fw-normal">0 = GRATUIT</span>
                                    </label>
                                    <input type="number" name="public_price" id="editPublicPrice" class="form-control"
                                        min="0" step="1">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold text-sm mb-8">Prix privé min (FCFA)</label>
                                    <input type="number" name="private_price_min" id="editPriceMin"
                                        class="form-control" min="0" step="1">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold text-sm mb-8">Prix privé max (FCFA)</label>
                                    <input type="number" name="private_price_max" id="editPriceMax"
                                        class="form-control" min="0" step="1">
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 3 : Calendrier vaccinal ────────────────────── --}}
                        <div class="section-block mb-24">
                            <div class="d-flex align-items-center gap-2 mb-16">
                                <span class="badge rounded-pill text-white fw-semibold px-12 py-6"
                                    style="background:#0891B2;font-size:.7rem">03</span>
                                <h6 class="fw-bold mb-0 text-sm">Calendrier vaccinal</h6>
                                <hr class="flex-fill my-0 ms-2">
                            </div>
                            <div class="row gy-16">
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Phase / Nom de la dose</label>
                                    <input type="text" name="schedule[phase_name]" id="editPhaseName"
                                        class="form-control">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label fw-semibold text-sm mb-8">N° de dose</label>
                                    <input type="number" name="schedule[dose_number]" id="editDoseNumber"
                                        class="form-control" min="1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Libellé âge</label>
                                    <input type="text" name="schedule[age_label]" id="editAgeLabel"
                                        class="form-control">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label fw-semibold text-sm mb-8">Âge min (mois)</label>
                                    <input type="number" name="schedule[min_age_months]" id="editMinAge"
                                        class="form-control" min="0" step="0.5">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label fw-semibold text-sm mb-8">Âge max (mois)</label>
                                    <input type="number" name="schedule[max_age_months]" id="editMaxAge"
                                        class="form-control" min="0" step="0.5">
                                </div>

                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Sexe concerné</label>
                                    <select name="schedule[gender]" id="editGender" class="form-select">
                                        <option value="all">Tous</option>
                                        <option value="masculin">Masculin</option>
                                        <option value="feminin">Féminin</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label fw-semibold text-sm mb-8">Priorité</label>
                                    <input type="number" name="schedule[priority]" id="editPriority"
                                        class="form-control" value="0">
                                </div>
                                <div class="col-sm-4 d-flex align-items-end pb-2">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="schedule[is_booster]"
                                            id="editIsBooster" value="1">
                                        <label class="form-check-label text-sm" for="editIsBooster">
                                            Vaccin de rappel
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label fw-semibold text-sm mb-8">Rappel tous les (mois)</label>
                                    <input type="number" name="schedule[booster_every_months]" id="editBoosterMonths"
                                        class="form-control" min="1">
                                </div>

                                <div class="col-12">
                                    <p class="form-label fw-semibold text-sm mb-8">Contextes spéciaux</p>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="schedule[only_pregnant]" id="e_pregnant" value="1">
                                            <label class="form-check-label text-sm" for="e_pregnant">Femmes enceintes
                                                uniquement</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="schedule[for_travelers]" id="e_travelers" value="1">
                                            <label class="form-check-label text-sm" for="e_travelers">Voyageurs</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="schedule[in_community]"
                                                id="e_community" value="1">
                                            <label class="form-check-label text-sm" for="e_community">Milieu
                                                collectif</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="schedule[for_health_workers]" id="e_health" value="1">
                                            <label class="form-check-label text-sm" for="e_health">Professionnels de
                                                santé</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="schedule[for_immunocompromised]" id="e_immuno" value="1">
                                            <label class="form-check-label text-sm" for="e_immuno">Immunodéprimés</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="schedule[for_seniors]"
                                                id="e_seniors" value="1">
                                            <label class="form-check-label text-sm" for="e_seniors">Personnes
                                                âgées</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="schedule[exposed_to_vectors]" id="e_vectors" value="1">
                                            <label class="form-check-label text-sm" for="e_vectors">Exposés aux
                                                vecteurs</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6" id="wrapTravelZoneEdit" style="display:none">
                                    <label class="form-label fw-semibold text-sm mb-8">Zone de voyage</label>
                                    <input type="text" name="schedule[travel_zone]" id="editTravelZone"
                                        class="form-control">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-sm mb-8">Note importante (calendrier)</label>
                                    <textarea name="schedule[important_note]" id="editImportantNote" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 4 : Restrictions ────────────────────────────── --}}
                        <div class="section-block mb-24">
                            <div class="d-flex align-items-center gap-2 mb-16">
                                <span class="badge rounded-pill text-white fw-semibold px-12 py-6"
                                    style="background:#DC2626;font-size:.7rem">04</span>
                                <h6 class="fw-bold mb-0 text-sm">Restrictions / Contre-indications</h6>
                                <hr class="flex-fill my-0 ms-2">
                            </div>
                            <div class="row gy-16">
                                <div class="col-12">
                                    <div class="d-flex flex-wrap gap-3 p-12 rounded-8"
                                        style="background:rgba(220,38,38,.04);border:1px solid rgba(220,38,38,.15)">
                                        <div class="form-check">
                                            <input class="form-check-input edit-restriction-checkbox" type="checkbox"
                                                name="restrictions[]" value="pregnancy" id="e_restr_preg">
                                            <label class="form-check-label text-sm" for="e_restr_preg">
                                                🤰 Grossesse
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input edit-restriction-checkbox" type="checkbox"
                                                name="restrictions[]" value="immunocompromised" id="e_restr_immuno">
                                            <label class="form-check-label text-sm" for="e_restr_immuno">
                                                🛡️ Immunodéprimés
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input edit-restriction-checkbox" type="checkbox"
                                                name="restrictions[]" value="allergy" id="e_restr_allergy">
                                            <label class="form-check-label text-sm" for="e_restr_allergy">
                                                ⚠️ Allergies
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-sm mb-8">Motif / explication</label>
                                    <textarea name="restriction_reason" id="editRestrictionReason" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 5 : Catégories ──────────────────────────────── --}}
                        <div class="section-block">
                            <div class="d-flex align-items-center gap-2 mb-16">
                                <span class="badge rounded-pill text-white fw-semibold px-12 py-6"
                                    style="background:#7C3AED;font-size:.7rem">05</span>
                                <h6 class="fw-bold mb-0 text-sm">Catégories</h6>
                                <hr class="flex-fill my-0 ms-2">
                            </div>
                            <div class="d-flex flex-wrap gap-2 p-12 rounded-8"
                                style="border:1px solid #e0e0e0;min-height:48px">
                                @foreach ($allCategories as $cat)
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input edit-cat-checkbox" type="checkbox"
                                            name="categories[]" value="{{ $cat->id_categorie }}"
                                            id="cat_e_{{ $cat->id_categorie }}">
                                        <label class="form-check-label text-sm" for="cat_e_{{ $cat->id_categorie }}">
                                            {{ $cat->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>{{-- /modal-body --}}

                    <div class="modal-footer border-top px-24" style="padding: inherit;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary px-24">
                            {{-- <iconify-icon icon="solar:diskette-bold" class="me-1"></iconify-icon> --}}
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════════
     JAVASCRIPT
     • Pré-remplissage complet du modal Modifier
     • Auto-slug modal Créer
     • Affichage conditionnel espèce (animal) + zone voyage
══════════════════════════════════════════════════════════════════════════ --}}
    <script>
        // ── Helpers ────────────────────────────────────────────────────────────────
        function setVal(id, val) {
            const el = document.getElementById(id);
            if (el) el.value = val ?? '';
        }

        function setChecked(id, bool) {
            const el = document.getElementById(id);
            if (el) el.checked = !!bool;
        }

        // ── Affichage conditionnel : espèce (animal) ──────────────────────────────
        function toggleSpecies(typeSelectId, wrapId) {
            const sel = document.getElementById(typeSelectId);
            const wrap = document.getElementById(wrapId);
            if (!sel || !wrap) return;
            const update = () => wrap.style.display = sel.value === 'animal' ? '' : 'none';
            sel.addEventListener('change', update);
            update();
        }
        toggleSpecies('createType', 'wrapSpeciesCreate');
        toggleSpecies('editType', 'wrapSpeciesEdit');

        // ── Affichage conditionnel : zone de voyage ───────────────────────────────
        function toggleTravelZone(checkboxId, wrapId) {
            const cb = document.getElementById(checkboxId);
            const wrap = document.getElementById(wrapId);
            if (!cb || !wrap) return;
            const update = () => wrap.style.display = cb.checked ? '' : 'none';
            cb.addEventListener('change', update);
            update();
        }
        toggleTravelZone('c_travelers', 'wrapTravelZoneCreate');
        toggleTravelZone('e_travelers', 'wrapTravelZoneEdit');

        // ── Auto-slug (modal Créer) ───────────────────────────────────────────────
        document.getElementById('createName')?.addEventListener('input', function() {
            const slug = document.getElementById('createSlug');
            if (!slug || slug.dataset.manual) return;
            slug.value = this.value
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        });
        document.getElementById('createSlug')?.addEventListener('input', function() {
            this.dataset.manual = this.value ? '1' : '';
        });

        // ── Pré-remplir le modal Modifier ─────────────────────────────────────────
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {

                const v = JSON.parse(btn.dataset.vaccine || '{}');
                console.log('Données du vaccin à éditer :', v);
                const cats = JSON.parse(btn.dataset.categories || '[]');
                const s = JSON.parse(btn.dataset.schedule || '{}');
                const restr = JSON.parse(btn.dataset.restrictions || '[]');
                const rstReason = btn.dataset.restrictionReason || '';

                // Action du formulaire
                document.getElementById('formEdit').action = `/vaccines/${v.id_vaccine}`;

                // ── Vaccin ──
                setVal('editName', v.name);
                setVal('editShortName', v.short_name);
                setVal('editSlug', v.slug);
                setVal('editDescription', v.description);
                setVal('editImportantInfo', v.important_info);
                setVal('editPublicPrice', v.public_price ?? 0);
                setVal('editPriceMin', v.private_price_min);
                setVal('editPriceMax', v.private_price_max);
                setVal('editTargetedDisease', v.targeted_disease);
                setVal('editAdminMode', v.administration_mode);
                setVal('editScientificType', v.scientific_type);
                setVal('editProtectedAgainst', v.protected_against);
                setVal('editTargetPublic', v.target_public);
                setVal('editSourceUrl', v.source_url);
                setVal('editTargetSpecies', v.target_species);
                setChecked('editActive', v.is_active);

                const typeEl = document.getElementById('editType');
                if (typeEl) {
                    typeEl.value = v.vaccine_type ?? 'human';
                    typeEl.dispatchEvent(new Event('change')); // maj espèce
                }

                const vsEl = document.getElementById('editValidationStatus');
                if (vsEl) vsEl.value = v.validation_status ?? 'pending';

                // ── Catégories ──
                document.querySelectorAll('.edit-cat-checkbox').forEach(cb => {
                    cb.checked = cats.includes(parseInt(cb.value));
                });

                // ── Schedule ──
                setVal('editPhaseName', s.phase_name);
                setVal('editDoseNumber', s.dose_number);
                setVal('editAgeLabel', s.age_label);
                setVal('editMinAge', s.min_age_months ?? 0);
                setVal('editMaxAge', s.max_age_months);
                setVal('editGender', s.gender ?? 'all'); // select
                const genderEl = document.getElementById('editGender');
                if (genderEl) genderEl.value = s.gender ?? 'all';
                setVal('editPriority', s.priority ?? 0);
                setChecked('editIsBooster', s.is_booster);
                setVal('editBoosterMonths', s.booster_every_months);
                setVal('editTravelZone', s.travel_zone);
                setVal('editImportantNote', s.important_note);

                // Contextes schedule (checkboxes booléens)
                setChecked('e_pregnant', s.only_pregnant);
                const travCb = document.getElementById('e_travelers');
                if (travCb) {
                    travCb.checked = !!s.for_travelers;
                    travCb.dispatchEvent(new Event('change')); // maj zone voyage
                }
                setChecked('e_community', s.in_community);
                setChecked('e_health', s.for_health_workers);
                setChecked('e_immuno', s.for_immunocompromised);
                setChecked('e_seniors', s.for_seniors);
                setChecked('e_vectors', s.exposed_to_vectors);

                // ── Restrictions ──
                document.querySelectorAll('.edit-restriction-checkbox').forEach(cb => {
                    cb.checked = restr.includes(cb.value);
                });
                setVal('editRestrictionReason', rstReason);
            });
        });
    </script>

@endsection
