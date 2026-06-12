@extends('layouts.master', ['title' => 'Pathologies'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Pathologies</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ url('index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste des pathologies</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="card h-100 p-0 radius-12">
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3"></div>
                <a href="#"
                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#addPathologieModal">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Ajouter une pathologie
                </a>
            </div>

            <div class="modal fade" id="addPathologieModal" tabindex="-1" aria-labelledby="addPathologieModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content radius-16 bg-base">
                        <div
                            class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0 bg-success text-white">
                            <h1 class="modal-title fs-5 text-white" id="addPathologieModalLabel">Ajout d'une nouvelle pathologie
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="color: white"></button>
                        </div>
                        <div class="modal-body p-24">
                            <form action="{{ route('pathologies.store') }}" method="post" role="form">
                                @csrf
                                <div class="row">
                                    <div class="col-12 mb-20">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Code</label>
                                        <input type="text" name="code" required class="form-control radius-8"
                                            placeholder="Saisir le code de la pathologie...">
                                    </div>
                                    <div class="col-12 mb-20">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nom</label>
                                        <input type="text" name="name" required class="form-control radius-8"
                                            placeholder="Saisir le nom de la pathologie...">
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                        <button type="reset" data-bs-dismiss="modal"
                                            class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">Annuler</button>
                                        <button type="submit"
                                            class="btn btn-primary border border-primary-600 text-md px-50 py-12 radius-8">Enregistrer</button>
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
                                <th scope="col" style="font-size: 13px">Code</th>
                                <th scope="col" style="font-size: 13px">Nom</th>
                                <th scope="col" style="font-size: 13px">Date de création</th>
                                <th scope="col" style="font-size: 13px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pathologie as $item)
                                <tr>
                                    <td style="font-size: 13px">
                                        <span
                                            class="bg-primary-focus text-primary-main px-16 py-4 rounded-pill fw-medium text-sm">{{ $item->code }}</span>
                                    </td>
                                    <td style="font-size: 13px">{{ $item->name }}</td>
                                    <td style="font-size: 13px">
                                        {{ \Carbon\Carbon::parse($item->created_at)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)"
                                            class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center me-2"
                                            data-bs-toggle="modal" data-bs-target="#edit{{ $item->id_pathologie }}">
                                            <iconify-icon icon="lucide:edit"></iconify-icon>
                                        </a>

                                        <div class="modal fade" id="edit{{ $item->id_pathologie }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content radius-16 bg-base">
                                                    <div
                                                        class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0 bg-secondary">
                                                        <h1 class="modal-title fs-5 text-white">Modification de la Pathologie
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-24">
                                                        <form action="{{ route('pathologies.update', $item->id_pathologie) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="row">
                                                                <div class="col-6 mb-20">
                                                                    <label
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Code</label>
                                                                    <input value="{{ $item->code }}" type="text"
                                                                        name="code" required
                                                                        class="form-control radius-8">
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Nom</label>
                                                                    <input value="{{ $item->name }}" type="text"
                                                                        name="name" required
                                                                        class="form-control radius-8">
                                                                </div>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                                                    <button type="reset" data-bs-dismiss="modal"
                                                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">Annuler</button>
                                                                    <button type="submit"
                                                                        class="btn btn-secondary border border-secondary-600 text-md px-50 py-12 radius-8">Modifier</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <a href="javascript:void(0)"
                                            class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#delete{{ $item->id_pathologie }}">
                                            <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                        </a>

                                        <div class="modal fade" id="delete{{ $item->id_pathologie }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content radius-16 bg-base">
                                                    <div
                                                        class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0 bg-danger">
                                                        <h1 class="modal-title fs-5 text-white">Suppression</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-24">
                                                        <form action="{{ route('pathologies.destroy', $item->id_pathologie) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="text-center">
                                                                <h6>Êtes-vous sûr de vouloir supprimer cette pathologie ?</h6>
                                                                <p class="text-sm text-muted">"{{ $item->name }}"</p>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                                                    <button type="button" data-bs-dismiss="modal"
                                                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">Annuler</button>
                                                                    <button type="submit"
                                                                        class="btn btn-danger border border-danger-600 text-md px-50 py-12 radius-8">Oui,
                                                                        Supprimer</button>
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
