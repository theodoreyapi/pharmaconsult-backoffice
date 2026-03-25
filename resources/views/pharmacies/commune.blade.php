@extends('layouts.master', ['title' => 'Commune'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Communes</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste des communes</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="card h-100 p-0 radius-12">
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3">

                </div>
                <a href="#"
                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#addExampleModal">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Ajouter une commune
                </a>
            </div>
            <div class="modal fade" id="addExampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                    <div class="modal-content radius-16 bg-base">
                        <div class="modal-header bg-success py-16 px-24 border border-top-0 border-start-0 border-end-0">
                            <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">Ajout d'une commune
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-24">
                            <form action="{{ route('commune.store') }}" method="post" role="form">
                                @csrf
                                <div class="row">
                                    <div class="col-6 mb-20">
                                        <label for="name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">Libelle</label>
                                        <input type="text" name="libelle" required class="form-control radius-8"
                                            id="name" placeholder="Saisir le libelle">
                                    </div>
                                    <div class="col-6 mb-20">
                                        <label for="name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">Description</label>
                                        <input type="text" name="description" required class="form-control radius-8"
                                            id="name" placeholder="Saisir une petite description">
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                        <button type="reset" data-bs-dismiss="modal" aria-label="Close"
                                            class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                            Annuler
                                        </button>
                                        <button type="submit"
                                            class="btn btn-primary border border-primary-600 text-md px-50 py-12 radius-8">
                                            Ajouter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-24">
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                        <thead>
                            <tr>
                                <th scope="col" style="font-size: 13px">Libelle</th>
                                <th scope="col" style="font-size: 13px">Description</th>
                                <th scope="col" style="font-size: 13px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($communes as $item)
                                <tr>
                                    <td style="font-size: 13px">
                                        {!! wordwrap($item->name, 20, '<br>') !!}
                                    </td>
                                    <td style="font-size: 13px">
                                        {!! wordwrap($item->description, 20, '<br>') !!}
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)"
                                            class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#edit{{ $item->id_commune }}">
                                            <iconify-icon icon="lucide:edit"></iconify-icon>
                                        </a>
                                        <div class="modal fade" id="edit{{ $item->id_commune }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                                                <div class="modal-content radius-16 bg-base">
                                                    <div
                                                        class="modal-header bg-secondary py-16 px-24 border border-top-0 border-start-0 border-end-0">
                                                        <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">
                                                            Modification
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-24">
                                                        <form action="{{ route('commune.update', $item->id_commune) }}"
                                                            method="post" role="form">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="row">
                                                                <div class="col-6 mb-20">
                                                                    <label for="name"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Libelle</label>
                                                                    <input type="text" name="libelle" required
                                                                        value="{{ $item->name }}"
                                                                        class="form-control radius-8" id="name"
                                                                        placeholder="Saisir le libelle">
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label for="name"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Description</label>
                                                                    <input type="text" name="description" required
                                                                        class="form-control radius-8" id="name"
                                                                        value="{{ $item->description }}"
                                                                        placeholder="Saisir une petite description">
                                                                </div>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                                                    <button type="reset" data-bs-dismiss="modal"
                                                                        aria-label="Close"
                                                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                                                        Annuler
                                                                    </button>
                                                                    <button type="submit"
                                                                        class="btn btn-secondary border border-secondary-600 text-md px-50 py-12 radius-8">
                                                                        Modifier
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0)"
                                            class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#delete{{ $item->id_commune }}">
                                            <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                        </a>
                                        <div class="modal fade" id="delete{{ $item->id_commune }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                                                <div class="modal-content radius-16 bg-base">
                                                    <div
                                                        class="modal-header bg-danger py-16 px-24 border border-top-0 border-start-0 border-end-0">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                                                            Suppression
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-24">
                                                        <form action="{{ route('commune.destroy', $item->id_commune) }}"
                                                            method="post" role="form">
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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
