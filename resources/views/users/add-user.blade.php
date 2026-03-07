@extends('layouts.master', ['title' => 'Ajouter utilisateur'])

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
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Ajouter un utilisateur</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tabeau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Ajouter utilisateur</li>
            </ul>
        </div>

        <div class="card h-100 p-0 radius-12">
            <div class="card-body p-24">
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-8 col-lg-10">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="text-md text-primary-light mb-16">Profile Image</h6>

                                <form action="#" method="post">
                                    @csrf
                                    <!-- Upload Image Start -->
                                    <div class="mb-24 mt-16">
                                        <div class="avatar-upload">
                                            <div
                                                class="avatar-edit position-absolute bottom-0 end-0 me-24 mt-16 z-1 cursor-pointer">
                                                <input name="photo" required type='file' id="imageUpload"
                                                    accept=".png, .jpg, .jpeg" hidden>
                                                <label for="imageUpload"
                                                    class="w-32-px h-32-px d-flex justify-content-center align-items-center bg-primary-50 text-primary-600 border border-primary-600 bg-hover-primary-100 text-lg rounded-circle">
                                                    <iconify-icon icon="solar:camera-outline" class="icon"></iconify-icon>
                                                </label>
                                            </div>
                                            <div class="avatar-preview">
                                                <div id="imagePreview"> </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Upload Image End -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-20">
                                                <label for="name"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                    Nom utilisateur
                                                    <span class="text-danger-600">*</span></label>
                                                <input required name="username" type="text" class="form-control radius-8"
                                                    id="name" placeholder="Entrez son nom utilisateur">
                                            </div>
                                            <div class="mb-20">
                                                <label for="name"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                    Nom
                                                    <span class="text-danger-600">*</span></label>
                                                <input required name="name" type="text" class="form-control radius-8"
                                                    id="name" placeholder="Entrez son nom">
                                            </div>
                                            <div class="mb-20">
                                                <label for="name"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                    Prénom
                                                    <span class="text-danger-600">*</span></label>
                                                <input required name="prenom" type="text" class="form-control radius-8"
                                                    id="name" placeholder="Entrez son prénom">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-20">
                                                <label for="email"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Email
                                                    <span class="text-danger-600">*</span></label>
                                                <input required name="email" type="email" class="form-control radius-8"
                                                    id="email" placeholder="Entrez son adresse email">
                                            </div>
                                            <div class="mb-20">
                                                <label for="number"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Téléphone</label>
                                                <input required name="phone" type="number" class="form-control radius-8"
                                                    id="number" placeholder="Entrez son numéro de tелефone">
                                            </div>
                                            <div class="mb-20">
                                                <label for="depart"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Pays
                                                    <span class="text-danger-600">*</span> </label>
                                                <select name="country" required class="form-control radius-8 form-select"
                                                    id="depart">
                                                    <option>Sélectionnez son pays </option>
                                                    <option value="">Enter Event Title One </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-20">
                                        <label for="desc"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">Apropos de
                                            lui</label>
                                        <textarea name="about" class="form-control radius-8" id="desc" placeholder="Ecrire une description..."></textarea>
                                    </div>
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
