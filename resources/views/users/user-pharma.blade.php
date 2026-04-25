@extends('layouts.master', ['title' => 'Pharmacien'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Pharmacien</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ url('index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste Pharmacien</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="card h-100 p-0 radius-12">
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3">
                </div>
                <a href="{{ url('add-admin') }}"
                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Ajouter pharmacien
                </a>
            </div>
            
            <div class="card-body p-24">
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                        <thead>
                            <tr>
                                <th scope="col" style="font-size: 13px">Nom</th>
                                <th scope="col" style="font-size: 13px">Contact</th>
                                <th scope="col" style="font-size: 13px">Solde</th>
                                <th scope="col" style="font-size: 13px">Solde Avant</th>
                                <th scope="col" style="font-size: 13px">Profil</th>
                                <th scope="col" style="font-size: 13px">Statut</th>
                                <th scope="col" style="font-size: 13px">Créé par</th>
                                <th scope="col" style="font-size: 13px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($admins as $item)
                                <tr>
                                    <td style="font-size: 13px">
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/user-list/user-list1.png" alt=""
                                                class="flex-shrink-0 me-12 radius-8">
                                            <strong style="font-size: 13px">
                                                {!! wordwrap($item->first_name, 20, '<br>') !!} {!! wordwrap($item->last_name, 20, '<br>') !!}
                                                <br>
                                                <span style="color: rgb(133, 133, 241)">{{ $item->pharmacy_name }}</span>
                                            </strong>
                                        </div>
                                    </td>
                                    <td style="font-size: 13px">
                                        {{ $item->email }}
                                        <br>
                                        {{ $item->phone_number }}
                                    </td>
                                    <td style="font-size: 13px; color: #28a745">
                                        {{ $item->amount }} FCFA
                                    </td>
                                    <td style="font-size: 13px; color: #dc3545">
                                        {{ $item->last_amount }} FCFA
                                    </td>
                                    <td style="font-size: 13px">
                                        @if ($item->role == 'PHARMACIEN')
                                            <span
                                                class="badge text-sm fw-semibold text-info-600 bg-info-100 px-20 py-9 radius-4 text-white">PHARMACIEN</span>
                                        @elseif ($item->role == 'GESTIONNAIRE')
                                            <span
                                                class="badge text-sm fw-semibold text-neutral-800 bg-neutral-300 px-20 py-9 radius-4 text-white">GESTIONNAIRE</span>
                                        @else
                                            <span
                                                class="badge text-sm fw-semibold text-lilac-600 bg-lilac-100 px-20 py-9 radius-4 text-white">CAISSIERE</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 13px">
                                        @if ($item->active == 'ACTIVE')
                                            <span
                                                class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>
                                        @else
                                            <span
                                                class="bg-warning-focus text-warning-main px-24 py-4 rounded-pill fw-medium text-sm">Inactive</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 13px">
                                        {!! wordwrap($item->created_by, 20, '<br>') !!}
                                    </td>
                                    <td style="font-size: 13px">
                                        <a href="javascript:void(0)"
                                            class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#edit{{ $item->id_pharmacien }}">
                                            <iconify-icon icon="lucide:edit"></iconify-icon>
                                        </a>
                                        <div class="modal fade" id="edit{{ $item->id_pharmacien }}" tabindex="-1"
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
                                                        <form action="{{ route('user-pharma.update', $item->id_pharmacien) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="row">
                                                                <div class="col-6 mb-20">
                                                                    <label for="name"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                                        Nom
                                                                    </label>
                                                                    <input type="text" name="firstname" required
                                                                        class="form-control radius-8" id="name"
                                                                        placeholder="Entrer son nom"
                                                                        value="{{ $item->first_name }}">
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label for="name"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                                        Prénom
                                                                    </label>
                                                                    <input type="text" name="lastname" required
                                                                        class="form-control radius-8" id="name"
                                                                        placeholder="Entrer son prénom"
                                                                        value="{{ $item->last_name }}">
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label for="name"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                                        E-mail
                                                                    </label>
                                                                    <input type="email" name="email" required
                                                                        class="form-control radius-8" id="name"
                                                                        placeholder="Entrer son e-mail"
                                                                        value="{{ $item->email }}">
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label for="name"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                                        Téléphone
                                                                    </label>
                                                                    <input type="text" name="phone"
                                                                        class="form-control radius-8" id="name"
                                                                        placeholder="Entrer son numéro de Téléphone"
                                                                        value="{{ $item->phone_number }}">
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label for="name"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">
                                                                        Mot de passe
                                                                    </label>
                                                                    <input type="password" name="password"
                                                                        class="form-control radius-8" id="name"
                                                                        placeholder="Entrer son mot de passe">
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label for="country"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Profil
                                                                    </label>
                                                                    <select required name="profil"
                                                                        class="form-control radius-8 form-select"
                                                                        id="country">
                                                                        <option
                                                                            @if ($item->role == 'PHARMACIEN') selected @endif
                                                                            value="PHARMACIEN">PHARMACIEN</option>
                                                                        <option
                                                                            @if ($item->role == 'GESTIONNAIRE') selected @endif
                                                                            value="GESTIONNAIRE">GESTIONNAIRE</option>
                                                                        <option
                                                                            @if ($item->role == 'CAISSIERE') selected @endif
                                                                            value="CAISSIERE">CAISSIERE</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label for="country"
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Statut
                                                                    </label>
                                                                    <select required name="active"
                                                                        class="form-control radius-8 form-select"
                                                                        id="country">
                                                                        <option
                                                                            @if ($item->active == 'ACTIVE') selected @endif
                                                                            value="ACTIVE">Actif</option>
                                                                        <option
                                                                            @if ($item->active == 'INACTIVE') selected @endif
                                                                            value="INACTIVE">Inactif</option>
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
                                            data-bs-toggle="modal" data-bs-target="#delete{{ $item->id_pharmacien }}">
                                            <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                        </a>
                                        <div class="modal fade" id="delete{{ $item->id_pharmacien }}" tabindex="-1"
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
                                                            action="{{ route('user-pharma.destroy', $item->id_pharmacien) }}"
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
