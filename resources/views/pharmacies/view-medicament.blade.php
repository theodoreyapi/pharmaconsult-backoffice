@extends('layouts.master', ['title' => 'Détail médicament'])

@push('csss')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .substitut-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #e7f0fd;
            color: #1a5fc8;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 999px;
            border: 1px solid #b3cef5;
        }

        .substitut-pill button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
            line-height: 1;
            color: #1a5fc8;
            padding: 0;
        }

        .sub-drop-item {
            padding: 9px 14px;
            font-size: 14px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
        }

        .sub-drop-item:hover {
            background: #f5f7fa;
        }

        .sub-drop-item.already {
            color: #aaa;
            cursor: default;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const input = document.getElementById('tag-input');
            const dropdown = document.getElementById('substitut-dropdown');
            const tagsBox = document.getElementById('tags-container');
            const hiddenBox = document.getElementById('hidden-substituts');
            const selectBox = document.getElementById('select-box');

            if (!input || !dropdown || !tagsBox || !hiddenBox || !selectBox) return;

            // ── Données depuis Laravel ────────────────────────────────────────
            const allMeds = @json($tousLesMedicaments);

            // ── Pré-charger les substituts existants ─────────────────────────
            const existants = @json($medicaments['substitutes']);

            let selected = {};
            let timer = null;

            // Charger les substituts déjà enregistrés sous forme de pills
            existants.forEach(s => {
                addSubstitut(s.substitutId, s.substitutName);
            });

            // ── Événements ───────────────────────────────────────────────────
            input.addEventListener('focus', () => renderDropdown(filterMeds(input.value.trim())));
            selectBox.addEventListener('click', () => {
                input.focus();
                renderDropdown(filterMeds(input.value.trim()));
            });
            input.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(() => renderDropdown(filterMeds(this.value.trim())), 200);
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeDropdown();
                if (e.key === 'Backspace' && this.value === '') {
                    const ids = Object.keys(selected);
                    if (ids.length) removeSubstitut(parseInt(ids[ids.length - 1]));
                }
            });
            document.addEventListener('click', function(e) {
                if (!selectBox.contains(e.target) && !dropdown.contains(e.target)) {
                    closeDropdown();
                }
            });

            // ── Fonctions ─────────────────────────────────────────────────────
            function filterMeds(q) {
                const lower = q.toLowerCase();
                return q ?
                    allMeds.filter(m => m.name.toLowerCase().includes(lower)) :
                    allMeds;
            }

            function renderDropdown(items) {
                dropdown.innerHTML = !items.length ?
                    '<div style="padding:10px 14px; color:#999; font-size:14px;">Aucun résultat</div>' :
                    items.map(item => {
                        const isSel = !!selected[item.id_medicament];
                        return `
                    <div class="sub-drop-item ${isSel ? 'already' : ''}"
                         data-id="${item.id_medicament}"
                         data-name="${item.name}">
                        ${isSel ? '<span style="color:#1a5fc8;font-size:12px;margin-right:4px;">✓</span>' : ''}
                        ${item.name}
                        ${isSel ? '<small style="color:#aaa;float:right;">(sélectionné)</small>' : ''}
                    </div>`;
                    }).join('');

                dropdown.querySelectorAll('.sub-drop-item:not(.already)').forEach(el => {
                    el.addEventListener('click', () => addSubstitut(parseInt(el.dataset.id), el.dataset
                        .name));
                });

                dropdown.style.display = 'block';
            }

            function addSubstitut(id, name) {
                if (selected[id]) return;
                selected[id] = name;

                const pill = document.createElement('span');
                pill.className = 'substitut-pill';
                pill.dataset.id = id;
                pill.innerHTML = `${name} <button type="button" onclick="removeSubstitut(${id})">✕</button>`;
                tagsBox.insertBefore(pill, input);

                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'substituts[]';
                hidden.value = id;
                hidden.id = 'sub_hidden_' + id;
                hiddenBox.appendChild(hidden);

                input.value = '';
                renderDropdown(filterMeds(''));
                input.focus();
            }

            window.removeSubstitut = function(id) {
                delete selected[id];
                tagsBox.querySelector(`[data-id="${id}"]`)?.remove();
                document.getElementById('sub_hidden_' + id)?.remove();
                renderDropdown(filterMeds(input.value.trim()));
            };

            function closeDropdown() {
                dropdown.style.display = 'none';
            }
        });
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Détail médicament</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Médicament</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="row gy-4">
            <div class="col-lg-4">
                <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
                    <img height="50"
                        src="{{ $medicaments['medicamentPicture'] ?? URL::asset('assets/images/medicament.jpg') }}"
                        alt="" class=" object-fit-cover">
                    <div class="pb-24 ms-16 mb-24 me-16  mt--100">
                        <div class="text-center border border-top-0 border-start-0 border-end-0">
                            <img src="{{ $medicaments['medicamentPicture'] ?? URL::asset('assets/images/medicament.jpg') }}"
                                alt=""
                                class="border br-white border-width-2-px w-200-px h-200-px rounded-circle object-fit-cover">
                            <h6 class="mb-0 mt-16">{{ $medicaments['name'] }}</h6>
                            <span class="text-secondary-light mb-16 text-danger">{{ $medicaments['price'] }}</span>
                            <span class="text-secondary-light mb-16">{{ $medicaments['principeActif'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-body p-24">
                        <ul class="nav border-gradient-tab nav-pills mb-20 d-inline-flex" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center px-24 active" id="pills-edit-profile-tab"
                                    data-bs-toggle="pill" data-bs-target="#methodes" type="button" role="tab"
                                    aria-controls="pills-edit-profile" aria-selected="true">
                                    Notice
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center px-24" id="pills-change-passwork-tab"
                                    data-bs-toggle="pill" data-bs-target="#evaluations" type="button" role="tab"
                                    aria-controls="pills-change-passwork" aria-selected="false" tabindex="-1">
                                    Substitutes
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="methodes" role="tabpanel"
                                aria-labelledby="pills-edit-profile-tab" tabindex="0">
                                @if ($medicaments['notice'])
                                    <iframe src="{{ $medicaments['notice'] }}" width="100%" height="600px">
                                    </iframe>
                                @else
                                    <div class="alert alert-warning">
                                        La notice n’est pas disponible pour ce médicament.
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade" id="evaluations" role="tabpanel"
                                aria-labelledby="pills-change-passwork-tab" tabindex="0">
                                <div class="row">
                                    @forelse ($medicaments['substitutes'] as $item)
                                        <div class="col-md-5 mb-4">
                                            <div class="assurance-card d-flex align-items-center gap-2">
                                                <img height="50" width="50"
                                                    src="{{ $item['substitutImageId'] ?? URL::asset('assets/images/medicament.jpg') }}"
                                                    alt="{{ $item['substitutName'] }}" class="me-2">
                                                <span><strong>{{ $item['substitutName'] }} <br>
                                                        <p style="color: red">{{ $item['substitutPrice'] }}</p>
                                                    </strong></span>
                                            </div>
                                        </div>
                                    @empty
                                        <p>Aucun médicament trouvé.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-md text-primary-light mb-16">Photo</h6>
                        <form action="{{ route('medicament.update', $medicaments['id']) }}" method="post" role="form"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <!-- Upload Image Start -->
                            <div class="mb-24 mt-16">
                                <div class="avatar-upload">
                                    <input name="photo" type='file' id="imageUpload" accept=".png, .jpg, .jpeg">
                                </div>
                            </div>
                            <!-- Upload Image End -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-20">
                                        <label for="name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Nom
                                            <span class="text-danger-600">*</span></label>
                                        <input required name="name" type="text" class="form-control radius-8"
                                            id="name" value="{{ $medicaments['name'] }}"
                                            placeholder="Entrez le nom du médicament">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-20">
                                        <label for="email"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">Prix
                                            <span class="text-danger-600">*</span></label>
                                        <input required name="price" type="text" class="form-control radius-8"
                                            id="email" value="{{ $medicaments['price'] }}"
                                            placeholder="Entrez le prix du médicament">
                                    </div>
                                </div>
                                <div class="mb-20">
                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Principe actif
                                        <span class="text-danger-600">*</span></label>
                                    <input required name="principe" type="text" class="form-control radius-8"
                                        id="name" value="{{ $medicaments['principeActif'] }}"
                                        placeholder="Entrez le principe actif">
                                </div>
                            </div>
                            <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Notice
                                <span class="text-danger-600"></span>
                                <br>
                                <input name="notice" type='file' accept=".pdf" >
                            </label>
                            <br>
                            {{-- ── Substituts ──────────────────────────────────────────────── --}}
                            <div class="col-md-12 mt-3">
                                <div class="mb-20" style="position: relative;">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Substituts
                                    </label>

                                    <!-- BOX -->
                                    <div id="select-box" onclick="document.getElementById('tag-input').focus()"
                                        style="border: 1px solid #ced4da; border-radius: 8px; min-height: 42px;
                   padding: 6px 8px; display: flex; flex-wrap: wrap; gap: 6px;
                   align-items: center; cursor: text; background: #fff;">
                                        <div id="tags-container"
                                            style="display: flex; flex-wrap: wrap; gap: 6px; flex: 1; align-items: center;">
                                            <input id="tag-input" type="text"
                                                placeholder="Rechercher un médicament..." autocomplete="off"
                                                style="border: none; outline: none; background: transparent;
                           font-size: 14px; min-width: 140px; flex: 1;" />
                                        </div>
                                    </div>

                                    <!-- DROPDOWN -->
                                    <div id="substitut-dropdown"
                                        style="display: none; position: absolute; z-index: 9999; background: #fff;
                   border: 1px solid #ced4da; border-radius: 8px; margin-top: 2px;
                   max-height: 220px; overflow-y: auto; width: 100%;
                   box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                                    </div>

                                    <div id="hidden-substituts"></div>
                                    <small class="text-muted">Cliquez ou tapez pour filtrer — sélection multiple
                                        possible</small>
                                </div>
                            </div>
                            <br>
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                <button type="button"
                                    class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                    Annuler
                                </button>
                                <button type="submit"
                                    class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                                    Modifier
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
