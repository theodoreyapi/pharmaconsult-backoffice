@extends('layouts.master', ['title' => 'Catégories de vaccins'])

@section('content')
    <div class="dashboard-main-body">

        {{-- ── Fil d'Ariane ─────────────────────────────────────────────────────── --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Catégories de vaccins</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Catégories</li>
            </ul>
        </div>

        {{-- ── Alertes ──────────────────────────────────────────────────────────── --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <iconify-icon icon="solar:check-circle-bold" class="me-2"></iconify-icon>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                            <iconify-icon icon="solar:tag-bold" class="text-white text-xl"></iconify-icon>
                        </div>
                        <div>
                            <p class="text-secondary-light fw-medium mb-1 text-sm">Total catégories</p>
                            <h5 class="fw-bold mb-0">{{ $categories->total() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-none border h-100" style="border-left:4px solid #1565C0!important">
                    <div class="card-body p-20 d-flex align-items-center gap-3">
                        <div class="w-48-px h-48-px rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"
                            style="background:#1565C0">
                            <iconify-icon icon="solar:syringe-bold" class="text-white text-xl"></iconify-icon>
                        </div>
                        <div>
                            <p class="text-secondary-light fw-medium mb-1 text-sm">Total vaccins</p>
                            <h5 class="fw-bold mb-0">{{ $totalVaccines }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Card principale ──────────────────────────────────────────────────── --}}
        <div class="card shadow-none border">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 py-16 px-24">
                <h6 class="fw-semibold mb-0">Liste des catégories</h6>
                <div class="d-flex align-items-center gap-2">
                    {{-- Recherche --}}
                    <form method="GET" action="{{ route('categories.index') }}" class="d-flex align-items-center gap-2">
                        <div class="icon-field">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control form-control-sm w-auto" placeholder="Rechercher…">
                            <span class="icon">
                                <iconify-icon icon="ion:search-outline"></iconify-icon>
                            </span>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Filtrer</button>
                        @if (request('search'))
                            <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-danger">×</a>
                        @endif
                    </form>
                    {{-- Nouveau --}}
                    <button type="button" class="btn btn-sm text-white" style="background:#2E7D32" data-bs-toggle="modal"
                        data-bs-target="#modalCreate">
                        {{-- <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> --}}
                        Nouvelle catégorie
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
                                <th>Slug</th>
                                <th>Description</th>
                                <th class="text-center">Vaccins liés</th>
                                <th>Créé le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td class="text-secondary-light">{{ $category->id_categorie }}</td>
                                    <td>
                                        <span class="badge px-12 py-6 fw-semibold"
                                            style="background:rgba(46,125,50,.12);color:#2E7D32;border-radius:20px">
                                            {{ $category->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <code class="text-secondary-light text-sm">{{ $category->slug }}</code>
                                    </td>
                                    <td class="text-secondary-light" style="max-width:220px">
                                        <span class="text-truncate d-block">
                                            {{ $category->description ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-primary-focus text-primary px-12 py-4 rounded-pill fw-semibold">
                                            {{ $category->vaccines_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="text-secondary-light text-sm">
                                        {{ \Carbon\Carbon::parse($category->created_at)->format('d M Y') }}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            {{-- Modifier --}}
                                            <button
                                                class="w-32-px h-32-px d-flex justify-content-center align-items-center rounded-circle bg-primary-focus text-primary-600 btn-edit"
                                                title="Modifier" data-id="{{ $category->id_categorie }}"
                                                data-name="{{ $category->name }}" data-slug="{{ $category->slug }}"
                                                data-description="{{ $category->description }}" data-bs-toggle="modal"
                                                data-bs-target="#modalEdit">
                                                <iconify-icon icon="lucide:edit"></iconify-icon>
                                            </button>
                                            {{-- Supprimer --}}
                                            <form action="{{ route('categories.destroy', $category->id_categorie) }}"
                                                method="POST"
                                                onsubmit="return confirm('Supprimer « {{ $category->name }} » ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="w-32-px h-32-px d-flex justify-content-center align-items-center rounded-circle bg-danger-focus text-danger-main"
                                                    title="Supprimer">
                                                    <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-32 text-secondary-light">
                                        <iconify-icon icon="solar:tag-broken"
                                            class="text-4xl mb-2 d-block"></iconify-icon>
                                        Aucune catégorie trouvée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if ($categories->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center py-12 px-24">
                    <p class="text-secondary-light text-sm mb-0">
                        {{ $categories->firstItem() }}–{{ $categories->lastItem() }} sur {{ $categories->total() }}
                        catégories
                    </p>
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

    </div>{{-- /dashboard-main-body --}}

    {{-- ══════════════════════════════════════════════════════════════════════════
     MODAL — Créer
══════════════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalCreate" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content radius-12">
                <div class="modal-header border-bottom py-16 px-24">
                    <h6 class="modal-title fw-bold">
                        <iconify-icon icon="solar:add-circle-bold" class="me-2 text-success"></iconify-icon>
                        Nouvelle catégorie
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body py-24 px-24">
                        <div class="mb-16">
                            <label class="form-label fw-semibold text-sm mb-8">Nom <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Ex: Enfants < 5 ans"
                                required>
                        </div>
                        <div class="mb-16">
                            <label class="form-label fw-semibold text-sm mb-8">Slug <span
                                    class="text-secondary-light text-xs">(auto-généré si vide)</span></label>
                            <input type="text" name="slug" class="form-control" placeholder="ex: enfants-5-ans">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-sm mb-8">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Description optionnelle…"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-16 px-24">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn text-white" style="background:#2E7D32">
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content radius-12">
                <div class="modal-header border-bottom py-16 px-24">
                    <h6 class="modal-title fw-bold">
                        <iconify-icon icon="lucide:edit" class="me-2 text-primary"></iconify-icon>
                        Modifier la catégorie
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEdit" action="" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body py-24 px-24">
                        <div class="mb-16">
                            <label class="form-label fw-semibold text-sm mb-8">Nom <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="mb-16">
                            <label class="form-label fw-semibold text-sm mb-8">Slug</label>
                            <input type="text" name="slug" id="editSlug" class="form-control">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-sm mb-8">Description</label>
                            <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-16 px-24">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Pré-remplir le modal Modifier
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                document.getElementById('formEdit').action = `/categories/${id}`;
                document.getElementById('editName').value = btn.dataset.name;
                document.getElementById('editSlug').value = btn.dataset.slug;
                document.getElementById('editDescription').value = btn.dataset.description ?? '';
            });
        });

        // Auto-génération du slug depuis le nom (modal créer)
        document.querySelector('[name="name"]')?.addEventListener('input', function() {
            const slugField = document.querySelector('[name="slug"]');
            if (!slugField.value) {
                slugField.value = this.value
                    .toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });
    </script>
@endsection
