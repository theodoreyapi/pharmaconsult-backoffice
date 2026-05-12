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
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content radius-12">
                                            <div class="modal-header border-bottom py-16 px-24">
                                                <h6 class="modal-title fw-bold">
                                                    <iconify-icon icon="solar:syringe-bold"
                                                        class="me-2 text-success"></iconify-icon>
                                                    {{ $vaccine->name }}
                                                </h6>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-24 px-24">
                                                <div class="row gy-3">
                                                    <div class="col-sm-6">
                                                        <p class="text-sm text-secondary-light mb-4">Nom complet</p>
                                                        <p class="fw-semibold mb-0">{{ $vaccine->name }}</p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <p class="text-sm text-secondary-light mb-4">Nom court / Slug</p>
                                                        <p class="fw-semibold mb-0">{{ $vaccine->short_name ?? '—' }} /
                                                            <code>{{ $vaccine->slug }}</code>
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <p class="text-sm text-secondary-light mb-4">Type</p>
                                                        <p class="fw-semibold mb-0">{{ ucfirst($vaccine->vaccine_type) }}
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <p class="text-sm text-secondary-light mb-4">Statut</p>
                                                        <span
                                                            class="badge {{ $vaccine->is_active ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' }} px-12 py-4 rounded-pill">
                                                            {{ $vaccine->is_active ? 'Actif' : 'Inactif' }}
                                                        </span>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <p class="text-sm text-secondary-light mb-4">Prix public</p>
                                                        <p class="fw-bold mb-0 text-success-main">
                                                            {{ $vaccine->public_price == 0 ? 'GRATUIT' : number_format($vaccine->public_price, 0, ',', ' ') . ' ' . $vaccine->currency }}
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <p class="text-sm text-secondary-light mb-4">Prix privé min</p>
                                                        <p class="fw-semibold mb-0">
                                                            {{ $vaccine->private_price_min ? number_format($vaccine->private_price_min, 0, ',', ' ') . ' ' . $vaccine->currency : '—' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <p class="text-sm text-secondary-light mb-4">Prix privé max</p>
                                                        <p class="fw-semibold mb-0">
                                                            {{ $vaccine->private_price_max ? number_format($vaccine->private_price_max, 0, ',', ' ') . ' ' . $vaccine->currency : '—' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-12">
                                                        <p class="text-sm text-secondary-light mb-4">Description</p>
                                                        <p class="mb-0">{{ $vaccine->description ?? '—' }}</p>
                                                    </div>
                                                    <div class="col-12">
                                                        <p class="text-sm text-secondary-light mb-4">Informations
                                                            importantes</p>
                                                        <div class="p-12 rounded-8"
                                                            style="background:rgba(21,101,192,.06);border-left:3px solid #1565C0">
                                                            {{ $vaccine->important_info ?? '—' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <p class="text-sm text-secondary-light mb-4">Catégories associées
                                                        </p>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @forelse($vaccine->categories as $cat)
                                                                <span class="badge px-12 py-6 fw-semibold"
                                                                    style="background:rgba(46,125,50,.12);color:#2E7D32;border-radius:20px">
                                                                    {{ $cat->name }}
                                                                </span>
                                                            @empty
                                                                <span class="text-secondary-light">Aucune catégorie</span>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Fermer</button>
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

    {{-- ══════════════════════════════════════════════════════════════════════════
     MODAL — Créer
══════════════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalCreate" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content radius-12">
                <div class="modal-header border-bottom py-16 px-24">
                    <h6 class="modal-title fw-bold">
                        <iconify-icon icon="solar:add-circle-bold" class="me-2 text-success"></iconify-icon>
                        Nouveau vaccin
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('vaccins.store') }}" method="POST">
                    @csrf
                    <div class="modal-body py-24 px-24">
                        <div class="row gy-16">

                            <div class="col-sm-8">
                                <label class="form-label fw-semibold text-sm mb-8">Nom <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="Ex: VAT (Vaccin Anti-Tétanique)" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold text-sm mb-8">Nom court</label>
                                <input type="text" name="short_name" class="form-control" placeholder="Ex: VAT">
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold text-sm mb-8">Slug <span
                                        class="text-secondary-light text-xs">(auto)</span></label>
                                <input type="text" name="slug" id="createSlug" class="form-control"
                                    placeholder="vat-vaccin-anti-tetanique">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold text-sm mb-8">Type <span
                                        class="text-danger">*</span></label>
                                <select name="vaccine_type" class="form-select" required>
                                    <option value="human">Humain</option>
                                    <option value="animal">Animal</option>
                                </select>
                            </div>

                            <div class="col-sm-4">
                                <label class="form-label fw-semibold text-sm mb-8">Prix public (FCFA)</label>
                                <input type="number" name="public_price" class="form-control" value="0"
                                    min="0" step="0.01" placeholder="0 = GRATUIT">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold text-sm mb-8">Prix privé min (FCFA)</label>
                                <input type="number" name="private_price_min" class="form-control" min="0"
                                    step="0.01" placeholder="Ex: 2000">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold text-sm mb-8">Prix privé max (FCFA)</label>
                                <input type="number" name="private_price_max" class="form-control" min="0"
                                    step="0.01" placeholder="Ex: 3500">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm mb-8">Description</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Ex: Protection contre le tétanos"></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm mb-8">Informations importantes</label>
                                <textarea name="important_info" class="form-control" rows="2" placeholder="Ex: Série de 3 doses nécessaires"></textarea>
                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold mb-16 text-success">
                                Calendrier vaccinal
                            </h6>

                            <div class="row gy-16">

                                <div class="col-sm-4">
                                    <label class="form-label">Âge minimum (mois)</label>
                                    <input type="number" name="schedule[min_age_months]" class="form-control"
                                        min="0" value="0">
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label">Âge maximum (mois)</label>
                                    <input type="number" name="schedule[max_age_months]" class="form-control"
                                        min="0">
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label">Libellé âge</label>
                                    <input type="text" name="schedule[age_label]" class="form-control"
                                        placeholder="Ex: Dès la naissance">
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label">Sexe</label>
                                    <select name="schedule[gender]" class="form-select">
                                        <option value="all">Tous</option>
                                        <option value="masculin">Masculin</option>
                                        <option value="feminin">Féminin</option>
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label">Numéro de dose</label>
                                    <input type="number" name="schedule[dose_number]" class="form-control"
                                        min="1">
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label">Priorité</label>
                                    <input type="number" name="schedule[priority]" class="form-control" value="0">
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="schedule[is_booster]"
                                            value="1">
                                        <label class="form-check-label">
                                            Vaccin de rappel
                                        </label>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label">
                                        Rappel tous les X mois
                                    </label>

                                    <input type="number" name="schedule[booster_every_months]" class="form-control">
                                </div>

                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold mb-16 text-danger">
                                Restrictions
                            </h6>

                            <div class="row gy-16">

                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="restrictions[]"
                                            value="pregnancy" id="pregnancy">

                                        <label class="form-check-label" for="pregnancy">
                                            Grossesse
                                        </label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="restrictions[]"
                                            value="immunocompromised" id="immuno">

                                        <label class="form-check-label" for="immuno">
                                            Immunodéprimés
                                        </label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="restrictions[]"
                                            value="allergy" id="allergy">

                                        <label class="form-check-label" for="allergy">
                                            Allergies
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        Motif / explication
                                    </label>

                                    <textarea name="restriction_reason" class="form-control" rows="2"></textarea>
                                </div>

                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm mb-8">Catégories</label>
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

                            <div class="col-12 d-flex align-items-center gap-2">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        id="createActive" checked>
                                    <label class="form-check-label fw-semibold text-sm" for="createActive">Vaccin
                                        actif</label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer border-top py-16 px-24">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn text-white" style="background:#2E7D32">
                            {{-- <iconify-icon icon="solar:diskette-bold" class="me-1"></iconify-icon> --}}
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════
     MODAL — Modifier
══════════════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content radius-12">
                <div class="modal-header border-bottom py-16 px-24">
                    <h6 class="modal-title fw-bold">
                        <iconify-icon icon="lucide:edit" class="me-2 text-primary"></iconify-icon>
                        Modifier le vaccin
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEdit" action="" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body py-24 px-24">
                        <div class="row gy-16">

                            <div class="col-sm-8">
                                <label class="form-label fw-semibold text-sm mb-8">Nom <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="editName" class="form-control" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold text-sm mb-8">Nom court</label>
                                <input type="text" name="short_name" id="editShortName" class="form-control">
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold text-sm mb-8">Slug</label>
                                <input type="text" name="slug" id="editSlug" class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold text-sm mb-8">Type</label>
                                <select name="vaccine_type" id="editType" class="form-select">
                                    <option value="human">Humain</option>
                                    <option value="animal">Animal</option>
                                </select>
                            </div>

                            <div class="col-sm-4">
                                <label class="form-label fw-semibold text-sm mb-8">Prix public (FCFA)</label>
                                <input type="number" name="public_price" id="editPublicPrice" class="form-control"
                                    min="0" step="0.01">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold text-sm mb-8">Prix privé min</label>
                                <input type="number" name="private_price_min" id="editPriceMin" class="form-control"
                                    min="0" step="0.01">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold text-sm mb-8">Prix privé max</label>
                                <input type="number" name="private_price_max" id="editPriceMax" class="form-control"
                                    min="0" step="0.01">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm mb-8">Description</label>
                                <textarea name="description" id="editDescription" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm mb-8">Informations importantes</label>
                                <textarea name="important_info" id="editImportantInfo" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm mb-8">Catégories</label>
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

                            <div class="col-12">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        id="editActive">
                                    <label class="form-check-label fw-semibold text-sm" for="editActive">Vaccin
                                        actif</label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer border-top py-16 px-24">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            {{-- <iconify-icon icon="solar:diskette-bold" class="me-1"></iconify-icon> --}}
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ── Pré-remplir le modal Modifier ────────────────────────────────────────────
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const v = JSON.parse(btn.dataset.vaccine);
                const cats = JSON.parse(btn.dataset.categories);

                document.getElementById('formEdit').action = `/vaccines/${v.id_vaccine}`;

                document.getElementById('editName').value = v.name ?? '';
                document.getElementById('editShortName').value = v.short_name ?? '';
                document.getElementById('editSlug').value = v.slug ?? '';
                document.getElementById('editDescription').value = v.description ?? '';
                document.getElementById('editImportantInfo').value = v.important_info ?? '';
                document.getElementById('editPublicPrice').value = v.public_price ?? 0;
                document.getElementById('editPriceMin').value = v.private_price_min ?? '';
                document.getElementById('editPriceMax').value = v.private_price_max ?? '';
                document.getElementById('editType').value = v.vaccine_type ?? 'human';
                document.getElementById('editActive').checked = !!v.is_active;

                // Cocher les catégories
                document.querySelectorAll('.edit-cat-checkbox').forEach(cb => {
                    cb.checked = cats.includes(parseInt(cb.value));
                });
            });
        });

        // ── Auto-slug (modal créer) ───────────────────────────────────────────────────
        document.querySelector('[name="name"]')?.addEventListener('input', function() {
            const slug = document.getElementById('createSlug');
            if (!slug.value) {
                slug.value = this.value
                    .toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });
    </script>

@endsection
