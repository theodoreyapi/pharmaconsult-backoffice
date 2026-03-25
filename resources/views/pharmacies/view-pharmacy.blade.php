@extends('layouts.master', ['title' => 'Profil de l\'utilisateur'])

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
            <h6 class="fw-semibold mb-0">Détails pharmacie</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
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
                <a href="{{ route('pharmacy.show', $pharmacys->id_pharmacy) }}"
                    class="btn btn-info text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Associé assurance
                </a>
                <a href="{{ route('pharmacy.edit', $pharmacys->id_pharmacy) }}"
                    class="btn btn-success text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Associé moyen de paiement
                </a>
            </div>
        </div>
        <br>

        @include('layouts.statuts')

        <div class="row gy-4">
            <div class="col-lg-5">
                <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
                    <img src="{{ $pharmacys->facade_image ?? URL::asset('assets/images/pharmacy.jpg') }}" alt=""
                        class="w-100 object-fit-cover">
                    <div class="pb-24 ms-16 mb-24 me-16  mt--100">
                        <div class="text-center border border-top-0 border-start-0 border-end-0">
                            <img src="{{ $pharmacys->facade_image ?? URL::asset('assets/images/pharmacy.jpg') }}"
                                alt=""
                                class="border br-white border-width-2-px w-200-px h-200-px rounded-circle object-fit-cover">
                            <h6 class="mb-0 mt-16">{{ $pharmacys->name }}</h6>
                            <span class="text-secondary-light mb-16">{{ $pharmacys->address }}</span>
                        </div>
                        <div class="mt-24">
                            <h6 class="text-xl mb-16">Infos pharmacie</h6>
                            <ul>
                                <li class="d-flex align-items-center gap-1 mb-12">
                                    <span class="w-30 text-md fw-semibold text-primary-light">Pharmacien</span>
                                    <span class="w-70 text-secondary-light fw-medium">: {{ $pharmacys->owner_name }}</span>
                                </li>
                                <li class="d-flex align-items-center gap-1 mb-12">
                                    <span class="w-30 text-md fw-semibold text-primary-light"> Téléphone</span>
                                    <span class="w-70 text-secondary-light fw-medium">:
                                        {{ $pharmacys->phone_number }}</span>
                                </li>
                                <li class="d-flex align-items-center gap-1 mb-12">
                                    <span class="w-30 text-md fw-semibold text-primary-light"> WhatsApp</span>
                                    <span class="w-70 text-secondary-light fw-medium">:
                                        {{ $pharmacys->whats_app_phone_number }}</span>
                                </li>
                                <li class="d-flex align-items-center gap-1 mb-12">
                                    <span class="w-30 text-md fw-semibold text-primary-light"> Commune</span>
                                    <span class="w-70 text-secondary-light fw-medium">:
                                        {{ $pharmacys->commune_name }}</span>
                                </li>
                                <li class="d-flex align-items-center gap-1 mb-12">
                                    <span class="w-30 text-md fw-semibold text-primary-light"> Heure ouverture</span>
                                    <span class="w-70 text-secondary-light fw-medium">:
                                        {{ $pharmacys->opening_hours }}</span>
                                </li>
                                <li class="d-flex align-items-center gap-1 mb-12">
                                    <span class="w-30 text-md fw-semibold text-primary-light"> Garde début</span>
                                    <span class="w-70 text-secondary-light fw-medium">:
                                        {{ \Carbon\Carbon::parse($pharmacys->start_garde_date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                    </span>
                                </li>
                                <li class="d-flex align-items-center gap-1 mb-12">
                                    <span class="w-30 text-md fw-semibold text-primary-light"> Garde fin</span>
                                    <span class="w-70 text-secondary-light fw-medium">:
                                        {{ \Carbon\Carbon::parse($pharmacys->end_garde_date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                    </span>
                                </li>
                            </ul>
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
                                    Méthode & Asssurances
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center px-24" id="pills-change-passwork-tab"
                                    data-bs-toggle="pill" data-bs-target="#evaluations" type="button" role="tab"
                                    aria-controls="pills-change-passwork" aria-selected="false" tabindex="-1">
                                    Evaluations
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="methodes" role="tabpanel"
                                aria-labelledby="pills-edit-profile-tab" tabindex="0">
                                <h6 class="text-md text-primary-light mb-16">METHODE DE PAIEMENT</h6>
                                <div class="mb-24 mt-16">
                                    <div class="row col-sm-12">
                                        @foreach ($paymentMethods as $methodes)
                                            <div class="col-sm-4">
                                                <li class="d-flex align-items-center gap-1 mb-12">
                                                    <img class="w-30"
                                                        src="{{ str_replace(' ', '%20', $methodes->paymentMethodPicture ?? URL::asset('assets/images/user-list/user-list1.png')) }}"
                                                        alt="" class="flex-shrink-0 me-12 radius-8">
                                                    <span class="w-70 text-secondary-light fw-medium">
                                                        {{ $methodes->name }}</span>
                                                </li>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <br>
                                <br>
                                <h6 class="text-md text-primary-light mb-16">ASSURANCES</h6>
                                <div class="mb-24 mt-16">
                                    <div class="row col-sm-12">
                                        @foreach ($assurances as $assurance)
                                            <div class="col-sm-4">
                                                <li class="d-flex align-items-center gap-1 mb-12">
                                                    <img class="w-30"
                                                        src="{{ $assurance->assurancePicture ?? URL::asset('assets/images/user-list/user-list1.png') }}"
                                                        alt="" class="flex-shrink-0 me-12 radius-8">
                                                    <span class="w-70 text-secondary-light fw-medium">
                                                        {{ $assurance->name }}</span>
                                                </li>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <style>
                                .rating-box {
                                    width: 130px;
                                    height: 130px;
                                    margin-right: auto;
                                    margin-left: auto;
                                    background-color: #41BA3E;
                                    color: #fff
                                }

                                .rating-label {
                                    font-weight: bold
                                }

                                .rating-bar {
                                    width: 300px;
                                    padding: 8px;
                                    border-radius: 5px
                                }

                                .bar-5 {
                                    width: 70%;
                                    height: 13px;
                                    background-color: #41BA3E;
                                    border-radius: 20px
                                }

                                .bar-4 {
                                    width: 30%;
                                    height: 13px;
                                    background-color: #41BA3E;
                                    border-radius: 20px
                                }

                                .bar-3 {
                                    width: 20%;
                                    height: 13px;
                                    background-color: #41BA3E;
                                    border-radius: 20px
                                }

                                .bar-2 {
                                    width: 10%;
                                    height: 13px;
                                    background-color: #41BA3E;
                                    border-radius: 20px
                                }

                                .bar-1 {
                                    width: 0%;
                                    height: 13px;
                                    background-color: #41BA3E;
                                    border-radius: 20px
                                }

                                .star-active {
                                    color: #41BA3E;
                                    margin-top: 10px;
                                    margin-bottom: 10px
                                }

                                .star-active:hover {
                                    color: #41BA3E;
                                    cursor: pointer
                                }

                                .star-inactive {
                                    color: #CFD8DC;
                                    margin-top: 10px;
                                    margin-bottom: 10px
                                }

                                .blue-text {
                                    color: #0091EA
                                }

                                .profile-pic {
                                    width: 90px;
                                    height: 90px;
                                    border-radius: 100%;
                                    margin-right: 30px
                                }

                                .pic {
                                    width: 80px;
                                    height: 80px;
                                    margin-right: 10px
                                }

                                .vote {
                                    cursor: pointer
                                }
                            </style>

                            <div class="tab-pane fade" id="evaluations" role="tabpanel"
                                aria-labelledby="pills-change-passwork-tab" tabindex="0">

                                <div class="row justify-content-center">
                                    <div class="card">
                                        <div class="row justify-content-left d-flex">
                                            <div class="col-md-4 d-flex flex-column justify-content-center">
                                                <div class="rating-box justify-content-center">
                                                    <h3 class="pt-4 text-white">
                                                        {{ $average ?? 0 }}</h3>
                                                    <p class="text-center">sur 5</p>
                                                </div>
                                                <div class="justify-content-center">
                                                    <span class="fa fa-star star-active mx-1">
                                                        <iconify-icon icon="iconamoon:star"></iconify-icon>
                                                    </span>
                                                    <span class="fa fa-star star-active mx-1">
                                                        <iconify-icon icon="iconamoon:star"></iconify-icon>
                                                    </span>
                                                    <span class="fa fa-star star-active mx-1">
                                                        <iconify-icon icon="iconamoon:star"></iconify-icon>
                                                    </span>
                                                    <span class="fa fa-star star-active mx-1">
                                                        <iconify-icon icon="iconamoon:star"></iconify-icon>
                                                    </span>
                                                    <span class="fa fa-star star-inactive mx-1">
                                                        <iconify-icon icon="iconamoon:star"></iconify-icon>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="rating-bar0 justify-content-center">
                                                    <table class="text-left mx-auto">
                                                        <tr>
                                                            <td class="rating-label">5</td>
                                                            <td class="rating-bar">
                                                                <div class="bar-container">
                                                                    <div class="bar-5"></div>
                                                                </div>
                                                            </td>
                                                            <td class="text-right">
                                                                {{ $counterFive ?? 0 }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="rating-label">4</td>
                                                            <td class="rating-bar">
                                                                <div class="bar-container">
                                                                    <div class="bar-4"></div>
                                                                </div>
                                                            </td>
                                                            <td class="text-right">
                                                                {{ $counterFour ?? 0 }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="rating-label">3</td>
                                                            <td class="rating-bar">
                                                                <div class="bar-container">
                                                                    <div class="bar-3"></div>
                                                                </div>
                                                            </td>
                                                            <td class="text-right">
                                                                {{ $counterThree ?? 0 }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="rating-label">2</td>
                                                            <td class="rating-bar">
                                                                <div class="bar-container">
                                                                    <div class="bar-2"></div>
                                                                </div>
                                                            </td>
                                                            <td class="text-right">
                                                                {{ $counterTwo ?? 0 }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="rating-label">1</td>
                                                            <td class="rating-bar">
                                                                <div class="bar-container">
                                                                    <div class="bar-1"></div>
                                                                </div>
                                                            </td>
                                                            <td class="text-right">
                                                                {{ $counterOne ?? 0 }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @foreach ($reviews ?? [] as $notices)
                                        <div class="card">
                                            <div class="row d-flex">
                                                <div class="d-flex flex-column">
                                                    <h6 class="mt-2 mb-0">{{ $notices->userName }}</h6>
                                                    <div>
                                                        <p class="text-left">
                                                            <span class="text-muted">{{ $notices->note }}</span>
                                                            <span class="fa fa-star star-active ml-3">
                                                                <iconify-icon icon="iconamoon:star"></iconify-icon>
                                                            </span>
                                                            <span class="fa fa-star star-active">
                                                                <iconify-icon icon="iconamoon:star"></iconify-icon>
                                                            </span>
                                                            <span class="fa fa-star star-active">
                                                                <iconify-icon icon="iconamoon:star"></iconify-icon>
                                                            </span>
                                                            <span class="fa fa-star star-active">
                                                                <iconify-icon icon="iconamoon:star"></iconify-icon>
                                                            </span>
                                                            <span class="fa fa-star star-inactive">
                                                                <iconify-icon icon="iconamoon:star"></iconify-icon>
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="ml-auto">
                                                    <p class="text-muted pt-5 pt-sm-3">{{ $notices->dateNotice }}</p>
                                                </div>
                                            </div>
                                            <div class="row text-left">
                                                <p class="content">
                                                    {{ $notices->details }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card h-100 p-0 radius-12">
                <div class="card-body p-24">
                    <div class="row justify-content-center">
                        <div class="col-xxl-12 col-xl-12 col-lg-12">
                            <div class="card border">
                                <h4>Modification</h4>
                                <div class="card-body">
                                    <h6 class="text-md text-primary-light mb-16">Photo</h6>

                                    <form action="{{ route('pharmacy.update', $pharmacys->id_pharmacy) }}" method="post"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH')
                                        <!-- Upload Image Start -->
                                        <div class="mb-24 mt-16">
                                            <div class="avatar-upload">
                                                <input name="photo" type='file' accept=".png, .jpg, .jpeg">
                                            </div>
                                        </div>
                                        <!-- Upload Image End -->
                                        <div class="row">
                                            <div class="row">
                                                <div class="mb-20 col-md-6">
                                                    <label for="name"
                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                        Libelle
                                                        <span class="text-danger-600">*</span></label>
                                                    <input required name="name" type="text"
                                                        class="form-control radius-8" id="name" value="{{ $pharmacys->name }}"
                                                        placeholder="Entrez le nom de la pharmacie">
                                                </div>
                                                <div class="mb-20 col-md-6">
                                                    <label for="name"
                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                        Adresse
                                                        <span class="text-danger-600">*</span></label>
                                                    <input required name="adresse" type="text"
                                                        class="form-control radius-8" id="name" value="{{ $pharmacys->address }}"
                                                        placeholder="Entrez l'adresse de la pharmacie">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-20 col-md-6">
                                                    <label for="email"
                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Responsable
                                                        <span class="text-danger-600">*</span></label>
                                                    <input required name="responsable" type="text"
                                                        class="form-control radius-8" id="email" value="{{ $pharmacys->owner_name }}"
                                                        placeholder="Entrez le nom du responsable">
                                                </div>
                                                <div class="mb-20 col-md-6">
                                                    <label for="depart"
                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Commune </label>
                                                    <select name="commune" required
                                                        class="form-control radius-8 form-select" id="depart">
                                                        <option value="{{ $pharmacys->id_commune }}">{{ $pharmacys->commune_name }}
                                                        </option>
                                                        @foreach ($communes as $item)
                                                            <option value="{{ $item->id_commune }}">{{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-20 col-md-6">
                                                    <label for="number"
                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Téléphone</label>
                                                    <input name="phone" type="tel" class="form-control radius-8" value="{{ $pharmacys->phone_number }}"
                                                        id="number" placeholder="Entrez le numéro de téléphone">
                                                </div>
                                                <div class="mb-20 col-md-6">
                                                    <label for="number"
                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">WhatsApp</label>
                                                    <input name="whatsapp" type="tel" class="form-control radius-8" value="{{ $pharmacys->whats_app_phone_number }}"
                                                        id="number" placeholder="Entrez le numéro whatsapp">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-20 col-md-12">
                                                <label for="number"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">GPS</label>
                                                <input name="longitude" type="text" class="form-control radius-8" value="{{ $pharmacys->gps_coordinates }}"
                                                    id="number" placeholder="Lien">
                                            </div>
                                        </div>
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
            </div>
        </div>
    </div>
@endsection
