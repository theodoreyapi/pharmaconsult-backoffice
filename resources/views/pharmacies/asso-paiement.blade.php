@extends('layouts.master', ['title' => 'Pharmacies'])

@push('scripts')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#imageUpload").change(function() {
            readURL(this);
        });

        function initializePasswordToggle(toggleSelector) {
            $(toggleSelector).on('click', function() {
                $(this).toggleClass("ri-eye-off-line");
                var input = $($(this).attr("data-toggle"));
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        }
        // Call the function
        initializePasswordToggle('.toggle-password');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Associés Moyen de paiement</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ url('index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Pharmacie</li>
            </ul>
        </div>

        <div
            class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
            <div class="d-flex align-items-center flex-wrap gap-3">
            </div>
            <div class="d-flex align-items-center flex-wrap gap-3">
                <a href="{{ url('view-pharmacy', $id) }}"
                    class="btn btn-info text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
                    Revenir a la pharmacie
                </a>
            </div>
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

        <div class="row gy-4">
            <div class="col-lg-12">
                <div class="card h-100">
                    <form action="{{ url('asso-paiement', $id) }}" method="post">
                        @csrf
                        <div class="card-body p-24 row">
                            @foreach ($paiements as $key => $item)
                                <div class="col-sm-4 mb-3">
                                    <div class="assurance-card d-flex align-items-center gap-2">
                                        <input class="form-check-input me-2" id="assurance_{{ $key }}"
                                            type="checkbox" name="paiements[]" value="{{ $item->id_moyen_payment }}"
                                            {{ in_array($item->id_moyen_payment, $selectedPaiement) ? 'checked' : '' }}>
                                        <label for="assurance_{{ $key }}"
                                            class="d-flex align-items-center gap-2 m-0 w-100">
                                            <img src="{{ str_replace(' ', '%20', $item->payment_method_picture ?? URL::asset('assets/images/user-list/user-list1.png')) }}"
                                                alt="{{ $item->name }}">
                                            <span class="text-secondary-light fw-medium">
                                                {{ $item->name }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <a href="{{ url('pharmacy') }}" type="button"
                                class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                Annuler
                            </a>
                            <button type="submit"
                                class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                                Associer
                            </button>
                        </div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
