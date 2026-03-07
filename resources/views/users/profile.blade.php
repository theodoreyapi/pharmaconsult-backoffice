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
            <h6 class="fw-semibold mb-0">Profil utilisateur</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Profil</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="row gy-4">
            <div class="col-lg-4">
                <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
                    <img src="{{ URL::asset('assets/images/PC.png') }}" alt="" class="w-100 object-fit-cover">
                    <div class="pb-24 ms-16 mb-24 me-16  mt--100">
                        <div class="text-center border border-top-0 border-start-0 border-end-0">
                            <img src="{{ session('user_data')['userDetails']['profilePicture'] ?? URL::asset('assets/images/user-grid/user-grid-img14.png') }}"
                                alt=""
                                class="border br-white border-width-2-px w-200-px h-200-px rounded-circle object-fit-cover">
                            <h6 class="mb-0 mt-16">{{ session('user_data')['firstName'] }}
                                {{ session('user_data')['lastName'] }}</h6>
                            <span class="text-secondary-light mb-16">{{ session('user_data')['email'] }}</span>
                        </div>
                        <div class="mt-24">
                            <h6 class="text-xl mb-16">Info personnelle</h6>
                            <ul>
                                <li class="d-flex align-items-center gap-1 mb-12">
                                    <span class="w-30 text-md fw-semibold text-primary-light">Telephone</span>
                                    <span class="w-70 text-secondary-light fw-medium">:
                                        {{ session('user_data')['phoneNumber'] }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body p-24">
                        <ul class="nav border-gradient-tab nav-pills mb-20 d-inline-flex" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center px-24 active"
                                    id="pills-change-passwork-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-change-passwork" type="button" role="tab"
                                    aria-controls="pills-change-passwork" aria-selected="false" tabindex="-1">
                                    Changer son mot de passe
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-change-passwork" role="tabpanel"
                                aria-labelledby="pills-change-passwork-tab" tabindex="0">
                                <form action="{{ url('profile') }}" method="post" role="form">
                                    @csrf
                                    <div class="mb-20">
                                        <label for="your-password"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">Mot de passe
                                            actuel
                                            <span class="text-danger-600">*</span></label>
                                        <div class="position-relative">
                                            <input name="password" required type="password" class="form-control radius-8"
                                                id="your-password" placeholder="Entrez votre mot de passe actuel*">
                                            <span
                                                class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                                                data-toggle="#your-password"></span>
                                        </div>
                                    </div>
                                    <div class="mb-20">
                                        <label for="confirm-password"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">Nouveau mot
                                            de
                                            passe<span class="text-danger-600">*</span></label>
                                        <div class="position-relative">
                                            <input name="cpassword" required type="password" class="form-control radius-8"
                                                id="confirm-password" placeholder="Nouveau mot de passe*">
                                            <span
                                                class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                                                data-toggle="#confirm-password"></span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center gap-3">
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
@endsection
