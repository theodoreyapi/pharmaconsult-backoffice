{{-- resources/views/pharmacies/medicament.blade.php --}}
@extends('layouts.master', ['title' => 'Medicaments'])

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput   = document.getElementById('search-input');
        const defaultList   = document.getElementById('default-medicament-list');
        const resultList    = document.getElementById('medicament-list');
        let timeout = null;

        searchInput.addEventListener('input', function () {
            const query = this.value.trim();

            if (query.length >= 3) {
                // Cacher la liste par défaut, afficher les résultats de recherche
                defaultList.style.display = 'none';
                resultList.style.display  = 'block';
                clearTimeout(timeout);

                timeout = setTimeout(() => {
                    fetch(`/search-medicaments?name=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            resultList.innerHTML = data.html;
                        })
                        .catch(() => {
                            resultList.innerHTML = '<p class="text-danger">Erreur lors de la recherche.</p>';
                        });
                }, 300);
            } else {
                // Revenir à la liste par défaut
                defaultList.style.display = 'block';
                resultList.style.display  = 'none';
                resultList.innerHTML      = '';
            }
        });
    });
</script>
@endpush

@section('content')
<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Medicaments</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Tableau de bord
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Liste des Medicaments</li>
        </ul>
    </div>

    @include('layouts.statuts')

    <div class="card h-100 p-0 radius-12">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
            <div class="d-flex align-items-center flex-wrap gap-3"></div>
            <a href="{{ url('add-medicament') }}"
                class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
                <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                Ajouter un medicament
            </a>
        </div>

        <div class="card-body p-24">
            {{-- Barre de recherche --}}
            <input type="text" id="search-input" class="form-control mb-3"
                placeholder="Rechercher un médicament (nom, principe actif, code CIP)...">

            {{-- Résultats de recherche AJAX --}}
            <div id="medicament-list" style="display: none;"></div>

            {{-- Liste par défaut avec pagination --}}
            <div id="default-medicament-list">
                @include('pharmacies.partials.medicament-list', ['medicaments' => $medicaments])

                {{-- Pagination Laravel native --}}
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <p class="text-muted mb-0">
                        Page {{ $medicaments->currentPage() }} sur {{ $medicaments->lastPage() }}
                        — {{ $medicaments->total() }} médicament(s) au total
                    </p>
                    <div class="d-flex gap-2">
                        @if ($medicaments->onFirstPage())
                            <button class="btn btn-outline-secondary" disabled>Précédent</button>
                        @else
                            <a href="{{ $medicaments->previousPageUrl() }}" class="btn btn-outline-primary">Précédent</a>
                        @endif

                        @if ($medicaments->hasMorePages())
                            <a href="{{ $medicaments->nextPageUrl() }}" class="btn btn-outline-primary">Suivant</a>
                        @else
                            <button class="btn btn-outline-secondary" disabled>Suivant</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
