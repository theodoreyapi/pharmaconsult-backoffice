@extends('layouts.master', ['title' => 'Détail médicament'])

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.summernote').summernote();
        });
    </script>

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
                    <img height="50" src="{{ $medicaments['medicamentPicture'] ?? URL::asset('assets/images/medicament.jpg') }}"
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
                                <p>
                                    {{ $medicaments['notice'] }}
                                </p>
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
                                            id="name" value="{{ $medicaments['name'] }}" placeholder="Entrez le nom du médicament">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-20">
                                        <label for="email"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">Prix
                                            <span class="text-danger-600">*</span></label>
                                        <input required name="price" type="text" class="form-control radius-8"
                                            id="email" value="{{ $medicaments['price'] }}" placeholder="Entrez le prix du médicament">
                                    </div>
                                </div>
                                <div class="mb-20">
                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                        Principe actif
                                        <span class="text-danger-600">*</span></label>
                                    <input required name="principe" type="text" class="form-control radius-8"
                                        id="name" value="{{ $medicaments['principeActif'] }}" placeholder="Entrez le principe actif">
                                </div>
                            </div>
                            <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Notice
                                <span class="text-danger-600"></span></label>
                            <textarea class="summernote" name="notice">
                                {{ $medicaments['notice'] }}
                            </textarea>
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
