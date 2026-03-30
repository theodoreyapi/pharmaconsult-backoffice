@extends('layouts.master', ['title' => 'Pharmacies'])

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#depart').on('change', function() {
            var communeId = $(this).val();
            $('#pharmacies-list').html('Chargement...'); // message temporaire

            if (communeId) {
                $.ajax({
                    url: '/proxy/pharmacies/' + communeId,
                    type: 'GET',
                    success: function(response) {
                        console.log(response);
                        // console.log(response.data);
                        let html = '';

                        if (response && response.length > 0) {
                            response.forEach(function(item, index) {
                                html += `
                                <div class="col-sm-4 mb-3">
                                    <div class="assurance-card d-flex align-items-center gap-2">
                                        <input class="form-check-input me-2" id="assurance_${index}"
                                            type="checkbox" name="pharmacys[]" value="${item.id_pharmacy}">
                                        <label for="assurance_${index}"
                                            class="d-flex align-items-center gap-2 m-0 w-100">
                                            <img src="${item.facade_image ? item.facade_image.replace(/ /g, '%20') : '/assets/images/user-list/user-list1.png'}"
                                                alt="${item.name}" width="40" height="40">
                                            <span class="text-secondary-light fw-medium">
                                                ${item.name}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            `;
                            });
                        } else {
                            html =
                                '<div class="col-12"><p class="text-muted">Aucune pharmacie trouvée pour cette commune.</p></div>';
                        }

                        $('#pharmacies-list').html(html);
                    },
                    error: function() {
                        $('#pharmacies-list').html(
                            '<div class="col-12"><p class="text-danger">Erreur lors de la récupération des pharmacies.</p></div>'
                        );
                    }
                });
            } else {
                $('#pharmacies-list').html('');
            }
        });
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Ajouter Pharmacie de Garde</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ url('index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Pharmacie de Garde</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <style>
            .assurance-card {
                border: 2px solid transparent;
                border-radius: 10px;
                padding: 10px;
                cursor: pointer;
                transition: border-color 0.3s;
            }

            .assurance-card input[type="checkbox"] {
                width: 20px;
                height: 20px;
                cursor: pointer;
            }

            .assurance-card.active {
                border-color: #41BA3E;
                /* Bleu Bootstrap */
            }

            .assurance-card img {
                width: 50px;
                height: 50px;
                object-fit: cover;
                border-radius: 8px;
            }

            .form-check-input {
                width: 20px;
                height: 20px;
                cursor: pointer;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const cards = document.querySelectorAll('.assurance-card');

                cards.forEach(card => {
                    const checkbox = card.querySelector('input[type="checkbox"]');

                    // Lorsqu'on clique sur la carte entière
                    card.addEventListener('click', () => {
                        // Simule un clic sur la checkbox (le navigateur gère checked automatiquement)
                        checkbox.click();
                    });

                    // Quand la checkbox change (checked ou non)
                    checkbox.addEventListener('change', () => {
                        if (checkbox.checked) {
                            card.classList.add('active');
                        } else {
                            card.classList.remove('active');
                        }
                    });

                    // Empêche que le clic direct sur le checkbox déclenche aussi le clic sur la carte
                    checkbox.addEventListener('click', e => e.stopPropagation());
                });
            });
        </script>
        <br><br>
        <form action="{{ route('garde.store') }}" method="post">
            @csrf
            <div class="row gy-4">
                <div class="col-lg-12 row">
                    <div class="mb-20 col-md-4">
                        <label for="depart" class="form-label fw-semibold text-primary-light text-sm mb-8">Commune
                            <span class="text-danger-600">*</span> </label>
                        <select name="commune" required class="form-control radius-8 form-select" id="depart">
                            <option value="">Sélectionnez la commune</option>
                            @foreach ($communes as $item)
                                <option value="{{ $item->id_commune }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-20 col-md-4">
                        <label for="depart" class="form-label fw-semibold text-primary-light text-sm mb-8">Début
                            <span class="text-danger-600">*</span> </label>
                        <input type="date" name="debut" required class="form-control mb-3">
                    </div>
                    <div class="mb-20 col-md-4">
                        <label for="depart" class="form-label fw-semibold text-primary-light text-sm mb-8">Fin
                            <span class="text-danger-600">*</span> </label>
                        <input type="date" name="fin" required class="form-control mb-3">
                    </div>
                    <div class="card">
                        <div class="card-body p-24 row">
                            <div class="row mt-3" id="pharmacies-list">

                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <button type="button"
                                class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                Annuler
                            </button>
                            <button type="submit"
                                class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                                Ajouter
                            </button>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
