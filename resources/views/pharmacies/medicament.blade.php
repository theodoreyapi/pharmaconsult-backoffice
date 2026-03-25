@extends('layouts.master', ['title' => 'Medicaments'])

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const defaultList = document.getElementById('default-medicament-list');
            const resultList = document.getElementById('medicament-list');
            let timeout = null;

            searchInput.addEventListener('input', function() {
                const query = this.value.trim();

                if (query.length >= 3) {
                    defaultList.style.display = 'none';
                    clearTimeout(timeout);

                    timeout = setTimeout(() => {
                        fetch(`/search-medicaments?name=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                document.getElementById('medicament-list').innerHTML = data
                                    .html;
                            });
                    }, 300);
                } else {
                    defaultList.style.display = 'block';
                    resultList.innerHTML = '';
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
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3"></div>
                <a href="{{ url('add-medicament') }}"
                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Ajouter un medicament
                </a>
            </div>
            <div class="card-body p-24">
                <div class="table-responsive scroll-sm">
                    <input type="text" id="search-input" class="form-control mb-3"
                        placeholder="Rechercher un médicament...">
                    <div id="medicament-list">
                        @include('pharmacies.partials.medicament-list', ['medicaments' => []])
                    </div>

                    <div id="default-medicament-list">
                        <div class="row">
                            @forelse ($medicaments as $item)
                                {{ $item['medicamentPicture'] }}
                                <div class="col-md-5 mb-4">
                                    <a
                                        href="{{ route('medicament.showAllGet', ['data' => urlencode(json_encode($item))]) }}">
                                        <div class="assurance-card d-flex align-items-center gap-2">
                                            <img height="50" width="50"
                                                src="{{ $item['medicamentPicture'] ?? URL::asset('assets/images/medicament.jpg') }}"
                                                alt="{{ $item['name'] }}" class="me-2">
                                            <span><strong>{{ $item['name'] }} <br>
                                                    <p style="color: red">{{ $item['price'] }}</p>
                                                </strong></span>
                                        </div>
                                    </a>
                                </div>
                                @if (Auth::user()->role == 'SUPERADMIN')
                                    <a href="javascript:void(0)"
                                        class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center"
                                        data-bs-toggle="modal" data-bs-target="#delete{{ $item->id_medicament }}">
                                        <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                    </a>
                                @endif
                                <div class="modal fade" id="delete{{ $item->id_medicament }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                                        <div class="modal-content radius-16 bg-base">
                                            <div
                                                class="modal-header bg-danger py-16 px-24 border border-top-0 border-start-0 border-end-0">
                                                <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">
                                                    Suppression
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-24">
                                                <form action="{{ route('medicament.destroy', $item->id_medicament) }}" method="post"
                                                    role="form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="row">
                                                        <label for="">Êtes-vous sûr de vouloir
                                                            supprimer?</label>
                                                        <div
                                                            class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                                            <button type="reset" data-bs-dismiss="modal"
                                                                aria-label="Close"
                                                                class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                                                Annuler
                                                            </button>
                                                            <button type="submit"
                                                                class="btn btn-danger border border-danger-600 text-md px-50 py-12 radius-8">
                                                                Supprimer
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p>Aucun médicament trouvé.</p>
                            @endforelse
                        </div>

                        <div class="pagination mt-4">
                            @if ($currentPage > 0)
                                <a href="{{ url()->current() }}?page={{ $currentPage - 1 }}"
                                    class="btn btn-outline-primary">Précédent</a>
                            @endif

                            @if ($currentPage + 1 < $lastPage)
                                <a href="{{ url()->current() }}?page={{ $currentPage + 1 }}"
                                    class="btn btn-outline-primary">Suivant</a>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
