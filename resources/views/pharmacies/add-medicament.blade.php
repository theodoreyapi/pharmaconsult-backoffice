@extends('layouts.master', ['title' => 'Ajouter un medicament'])

@push('csss')
    <link href = "https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel = "stylesheet" />
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.summernote').summernote();
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#substituts').select2({
                placeholder: "Rechercher un médicament",
                minimumInputLength: 2, // tape au moins 2 lettres
                ajax: {
                    url: "{{ route('medicament.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id_medicament,
                                    text: item.name
                                };
                            })
                        };
                    }
                }
            });
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
                                        <div class="col-md-6">
                                            <div class="mb-20">
                                                <label for="substituts"
                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                    Substituts
                                                    <span class="text-danger-600"></span></label>
                                                <select id="substituts" name="substituts[]" class="form-control"
                                                    multiple></select>
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
