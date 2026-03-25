@extends('layouts.master', ['title' => 'Termes et conditions'])

@push('scripts')
    <script src="{{ URL::asset('') }}assets/js/editor.highlighted.min.js"></script>
    <script src="{{ URL::asset('') }}assets/js/editor.quill.js"></script>
    <script src="{{ URL::asset('') }}assets/js/editor.katex.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.summernote').summernote();
        });
    </script>

    <script>
        // Editor Js Start
        const quill = new Quill('#editor', {
            modules: {
                syntax: true,
                toolbar: '#toolbar-container',
            },
            placeholder: 'Compose an epic...',
            theme: 'snow',
        });
        // Editor Js End

        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Politique de confidentialite</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Politique de confidentialite</li>
            </ul>
        </div>

        @include('layouts.statuts')

        @if ($abouts == null)
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3">
                </div>
                <a href="{{ url('add-politicy') }}"
                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Ajouter politique
                </a>
            </div>
            <div class="alert alert-warning" role="alert">
                <strong>Oups!</strong> Vous n'avez pas encore ajouté la politique de confidentialite. Veuillez ajouter la
                politique de confidentialite.
            </div>
        @else
            <div class="card basic-data-table radius-12 overflow-hidden">
                <form action="{{ route('terms-politicy.update', $abouts->id_politique) }}" method="post">
                    @csrf
                    @method('PATCH')
                    <textarea required class="summernote" name="contenu">
                {{ $abouts->contenu }}
                </textarea>

                    <div class="card-footer p-24 bg-base border border-bottom-0 border-end-0 border-start-0">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <button type="submit"
                                class="btn btn-primary border border-primary-600 text-md px-28 py-12 radius-8">
                                {{-- {{ $termes ? 'Mettre à jour' : 'Enregistrer' }} --}}
                                Mettre à jour
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
@endsection
