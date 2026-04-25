@extends('layouts.master', ['title' => 'Ajouter un medicament'])

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

            if (!input || !dropdown || !tagsBox || !hiddenBox || !selectBox) {
                console.error('Éléments introuvables dans le DOM');
                return;
            }

            // ─── Liste complète chargée depuis Laravel ───────────────────────
            const allMeds = @json($medicaments);
            // ─────────────────────────────────────────────────────────────────

            let selected = {};
            let timer = null;

            // Ouvrir le dropdown au focus ou au clic sur la box
            input.addEventListener('focus', function() {
                renderDropdown(filterMeds(this.value.trim()));
            });

            selectBox.addEventListener('click', function() {
                input.focus();
                renderDropdown(filterMeds(input.value.trim()));
            });

            // Filtrer à la saisie
            input.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    renderDropdown(filterMeds(this.value.trim()));
                }, 200);
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

            // ─── Filtrage local (pas de fetch) ───────────────────────────────
            function filterMeds(q) {
                if (!q) return allMeds; // pas de saisie = tout afficher
                const lower = q.toLowerCase();
                return allMeds.filter(m => m.name.toLowerCase().includes(lower));
            }

            function renderDropdown(items) {
                if (!items.length) {
                    dropdown.innerHTML =
                        '<div style="padding:10px 14px; color:#999; font-size:14px;">Aucun résultat</div>';
                } else {
                    dropdown.innerHTML = items.map(item => {
                        const isSel = !!selected[item.id_medicament];
                        return `
                    <div class="sub-drop-item ${isSel ? 'already' : ''}"
                         data-id="${item.id_medicament}"
                         data-name="${item.name}">
                        ${isSel ? '<span style="color:#1a5fc8; font-size:12px; margin-right:4px;">✓</span>' : ''}
                        ${item.name}
                        ${isSel ? '<small style="color:#aaa; float:right;">(sélectionné)</small>' : ''}
                    </div>`;
                    }).join('');

                    dropdown.querySelectorAll('.sub-drop-item:not(.already)').forEach(el => {
                        el.addEventListener('click', () => {
                            addSubstitut(parseInt(el.dataset.id), el.dataset.name);
                        });
                    });
                }
                dropdown.style.display = 'block';
            }

            function addSubstitut(id, name) {
                if (selected[id]) return;
                selected[id] = name;

                // Pill
                const pill = document.createElement('span');
                pill.className = 'substitut-pill';
                pill.dataset.id = id;
                pill.innerHTML = `${name} <button type="button" onclick="removeSubstitut(${id})">✕</button>`;
                tagsBox.insertBefore(pill, input);

                // Hidden input
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'substituts[]';
                hidden.value = id;
                hidden.id = 'sub_hidden_' + id;
                hiddenBox.appendChild(hidden);

                input.value = '';
                // Rafraîchir le dropdown pour marquer l'item comme sélectionné
                renderDropdown(filterMeds(''));
                input.focus();
            }

            window.removeSubstitut = function(id) {
                delete selected[id];
                const pill = tagsBox.querySelector(`[data-id="${id}"]`);
                if (pill) pill.remove();
                const hidden = document.getElementById('sub_hidden_' + id);
                if (hidden) hidden.remove();
                // Rafraîchir le dropdown
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
            <h6 class="fw-semibold mb-0">Ajouter medicament</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ url('/') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tabeau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Ajout de medicament</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="card h-100 p-0 radius-12">
            <div class="card-body p-24">
                <div class="row justify-content-center">
                    <div class="col-xxl-12 col-xl-12 col-lg-12">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="text-md text-primary-light mb-16">Photo</h6>
                                <form action="{{ route('medicament.store') }}" method="post" role="form"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <!-- Upload Image Start -->
                                    <div class="mb-20">
                                        <input class="form-control radius-8" name="photo" type="file"
                                            accept=".png, .jpg, .jpeg">
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
                                                    id="name" placeholder="Entrez le nom du médicament">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-20">
                                                <label for="email"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Prix
                                                    <span class="text-danger-600">*</span></label>
                                                <input required name="price" type="number" class="form-control radius-8"
                                                    id="email" placeholder="Entrez le prix du médicament">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-20">
                                                <label for="name"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                    Principe actif
                                                    <span class="text-danger-600">*</span></label>
                                                <input required name="principe" type="text" class="form-control radius-8"
                                                    id="name" placeholder="Entrez le principe actif">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-20">
                                                <label for="name"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                    Notice
                                                    <span class="text-danger-600"></span></label>
                                                <input name="notice" type="file" class="form-control radius-8"
                                                    id="name" placeholder="">
                                            </div>
                                        </div>
                                        {{-- BLOC SUBSTITUTS --}}
                                        <div class="col-md-6">
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

                                                <!-- HIDDEN INPUTS -->
                                                <div id="hidden-substituts"></div>

                                                <small class="text-muted">Cliquez ou tapez pour filtrer — sélection multiple
                                                    possible</small>
                                            </div>
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
                                            Enregistrer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
