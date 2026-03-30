@extends('layouts.master', ['title' => 'Ajouter une pharmacie'])

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
            <h6 class="fw-semibold mb-0">Ajouter pharmacie</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tabeau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Ajout de pharmacie</li>
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

                                <form action="{{ route('pharmacy.store') }}" method="post" enctype="multipart/form-data">
                                    @csrf
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
                                                <input required name="name" type="text" class="form-control radius-8"
                                                    id="name" placeholder="Entrez le nom de la pharmacie">
                                            </div>
                                            <div class="mb-20 col-md-6">
                                                <label for="name"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                    Adresse
                                                    <span class="text-danger-600">*</span></label>
                                                <input required name="adresse" type="text" class="form-control radius-8"
                                                    id="name" placeholder="Entrez l'adresse de la pharmacie">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-20 col-md-6">
                                                <label for="email"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Responsable
                                                    <span class="text-danger-600">*</span></label>
                                                <input required name="responsable" type="text"
                                                    class="form-control radius-8" id="email"
                                                    placeholder="Entrez le nom du responsable">
                                            </div>
                                            <div class="mb-20 col-md-6">
                                                <label for="depart"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Commune
                                                    <span class="text-danger-600">*</span> </label>
                                                <select name="commune" required class="form-control radius-8 form-select"
                                                    id="depart">
                                                    <option value="">Sélectionnez la commune de la pharmacie</option>
                                                    @foreach ($communes as $item)
                                                        <option value="{{ $item->id_commune }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-20 col-md-6">
                                                <label for="number"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Téléphone</label>
                                                <input name="phone" type="tel" class="form-control radius-8"
                                                    id="number" placeholder="Entrez le numéro de téléphone">
                                            </div>
                                            <div class="mb-20 col-md-6">
                                                <label for="number"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">WhatsApp</label>
                                                <input name="whatsapp" type="tel" class="form-control radius-8"
                                                    id="number" placeholder="Entrez le numéro whatsapp">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-20 col-md-12">
                                            <label for="number"
                                                class="form-label fw-semibold text-primary-light text-sm mb-8">GPS</label>
                                            <input name="longitude" type="text" class="form-control radius-8"
                                                id="number" placeholder="Longitude">
                                        </div>
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
