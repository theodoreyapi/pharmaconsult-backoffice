@extends('layouts.master', ['title' => 'Publicites'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Publicites</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ url('index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste des publicites</li>
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
                    Ajouter une publicite
                </a>
            </div>
            <div class="modal fade" id="addExampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                    <div class="modal-content radius-16 bg-base">
                        <div
                            class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0 bg-success text-white">
                            <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">
                                Ajout d'une nouvelle publicite
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="color: white"></button>
                        </div>
                        <div class="modal-body p-24">
                            <form action="{{ route('publicites.store') }}" method="post" role="form"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-12 mb-20">
                                        <label for="name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Image
                                        </label>
                                        <input type="file" name="image" required class="form-control radius-8"
                                            id="name">
                                    </div>
                                    <div class="col-6 mb-20">
                                        <label for="name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Libelle
                                        </label>
                                        <input type="text" name="libelle" required class="form-control radius-8"
                                            id="name" placeholder="Saisir un libellé">
                                    </div>
                                    <div class="col-6 mb-20">
                                        <label for="name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Lien
                                        </label>
                                        <input type="text" name="lien" required class="form-control radius-8"
                                            id="name" placeholder="Entrer le lien">
                                    </div>
                                    <div class="col-6 mb-20">
                                        <label for="name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Date debut
                                        </label>
                                        <input type="date" name="debut" required class="form-control radius-8"
                                            id="name">
                                    </div>
                                    <div class="col-6 mb-20">
                                        <label for="name"
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Date fin
                                        </label>
                                        <input type="date" name="fin" required class="form-control radius-8"
                                            id="name">
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                        <button type="reset" data-bs-dismiss="modal" aria-label="Close"
                                            class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                            Annuler
                                        </button>
                                        <button type="submit"
                                            class="btn btn-primary border border-primary-600 text-md px-50 py-12 radius-8">
                                            Enregistrer
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
                                <th scope="col" style="font-size: 13px"></th>
                                <th scope="col" style="font-size: 13px">Libelle</th>
                                <th scope="col" style="font-size: 13px">Coût</th>
                                <th scope="col" style="font-size: 13px">Debut</th>
                                <th scope="col" style="font-size: 13px">Fin</th>
                                <th scope="col" style="font-size: 13px">Statut</th>
                                <th scope="col" style="font-size: 13px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($publicites as $item)
                                <tr>
                                    <td>
                                        <img height="200" width="200"
                                            src="{{ $item->image ?? URL::asset('assets/images/user-list/user-list1.png') }}"
                                            alt="" class="radius-8">
                                    </td>
                                    <td>
                                        <strong style="font-size: 13px">
                                            <a href="{{ $item->lien }}" target="_blank">{{ $item->name }}</a>
                                        </strong>
                                    </td>
                                    <td>
                                        <strong style="font-size: 13px">
                                            {{ $item->price }}
                                        </strong>
                                    </td>
                                    <td style="font-size: 13px">
                                        {{ \Carbon\Carbon::parse($item->start_date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                    </td>
                                    <td style="font-size: 13px">
                                        {{ \Carbon\Carbon::parse($item->end_date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                    </td>
                                    <td>
                                        @if ($item->status == 'ACTIVE')
                                            <span
                                                class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>
                                        @else
                                            <span
                                                class="bg-warning-focus text-warning-main px-24 py-4 rounded-pill fw-medium text-sm">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)"
                                            class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#edit{{ $item->id_publicite }}">
                                            <iconify-icon icon="lucide:edit"></iconify-icon>
                                        </a>
                                        <div class="modal fade" id="edit{{ $item->id_publicite }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                                                <div class="modal-content radius-16 bg-base">
                                                    <div
                                                        class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0 bg-secondary">
                                                        <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">
                                                            Modification
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-24">
                                                        <form
                                                            action="{{ route('publicites.update', $item->id_publicite) }}"
                                                            method="post" role="form" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PATCH')

                                                            @php

                                                                $dateDebut = \Carbon\Carbon::parse(
                                                                    $item->start_date,
                                                                )->format('Y-m-d');
                                                                $dateFin = \Carbon\Carbon::parse(
                                                                    $item->end_date,
                                                                )->format('Y-m-d');
                                                            @endphp
                                                            <strong>
                                                                {{ $dateDebut }} - {{ $dateFin }}
                                                            </strong>
                                                            <div class="row">
                                                                <div class="col-12 mb-20">
                                                                    <img height="200" style="width: auto"
                                                                        src="{{ $item['image'] ?? URL::asset('assets/images/user-list/user-list1.png') }}"
                                                                        alt="" class="radius-8">
                                                                </div>
                                                                <div class="col-12 mb-20">
                                                                    <label for="name"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                                        Image
                                                                    </label>
                                                                    <input type="file" name="image"
                                                                        class="form-control radius-8" id="name">
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label for="name"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                                        Libelle
                                                                    </label>
                                                                    <input value="{{ $item->name }}" type="text"
                                                                        name="libelle" required
                                                                        class="form-control radius-8" id="name"
                                                                        placeholder="Saisir un libellé">
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label for="name"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                                        Lien
                                                                    </label>
                                                                    <input value="{{ $item->lien }}" type="text"
                                                                        name="lien" required
                                                                        class="form-control radius-8" id="name"
                                                                        placeholder="Entrer le lien">
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label for="name"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                                        Date debut
                                                                    </label>
                                                                    <input value="{{ $dateDebut }}" type="date"
                                                                        name="debut" required
                                                                        class="form-control radius-8" id="name">
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label for="name"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                                        Date fin
                                                                    </label>
                                                                    <input value="{{ $dateFin }}" type="date"
                                                                        name="fin" required
                                                                        class="form-control radius-8" id="name">
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label for="country"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Statut
                                                                    </label>
                                                                    <select required name="statut"
                                                                        class="form-control radius-8 form-select"
                                                                        id="country">
                                                                        <option value="{{ $item->status }}">Sélectionne
                                                                        </option>
                                                                        <option value="ACTIVE">ACTIVE</option>
                                                                        <option value="INACTIVE">INACTIVE</option>
                                                                    </select>
                                                                </div>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                                                    <button type="reset" data-bs-dismiss="modal"
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
                                            data-bs-toggle="modal" data-bs-target="#delete{{ $item->id_publicite }}">
                                            <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                        </a>
                                        <div class="modal fade" id="delete{{ $item->id_publicite }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                                                <div class="modal-content radius-16 bg-base">
                                                    <div
                                                        class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0 bg-danger">
                                                        <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">
                                                            Suppression
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-24">
                                                        <form
                                                            action="{{ route('publicites.destroy', $item->id_publicite) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="row">
                                                                Êtes-vous sûr de vouloir supprimer?
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                                                    <button type="reset" data-bs-dismiss="modal"
                                                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                                                        Annuler
                                                                    </button>
                                                                    <button type="submit"
                                                                        class="btn btn-danger border border-danger-600 text-md px-50 py-12 radius-8">
                                                                        Oui, Supprimer
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
