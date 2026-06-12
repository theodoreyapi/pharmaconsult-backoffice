@extends('layouts.master', ['title' => 'Conseils de santé'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Conseils de Santé</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ url('index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste des conseils</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="card h-100 p-0 radius-12">
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3"></div>
                <a href="#"
                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#addConseilModal">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Ajouter un conseil
                </a>
            </div>

            <div class="modal fade" id="addConseilModal" tabindex="-1" aria-labelledby="addConseilModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content radius-16 bg-base">
                        <div
                            class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0 bg-success text-white">
                            <h1 class="modal-title fs-5 text-white" id="addConseilModalLabel">Ajout d'un nouveau conseil
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="color: white"></button>
                        </div>
                        <div class="modal-body p-24">
                            <form action="{{ route('conseils.store') }}" method="post" role="form">
                                @csrf
                                <div class="row">
                                    <div class="col-6 mb-20">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Type</label>
                                        <select required name="type" class="form-control radius-8 form-select">
                                            <option value="">Sélectionne un type</option>
                                            <option value="Conseil">Conseil</option>
                                            <option value="Article">Article</option>
                                            <option value="Astuce">Astuce</option>
                                        </select>
                                    </div>
                                    <div class="col-6 mb-20">
                                        <label
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">Catégorie</label>
                                        <select required name="categorie" class="form-control radius-8 form-select">
                                            <option value="">Sélectionne une catégorie</option>
                                            <option value="Hypertension">Hypertension</option>
                                            <option value="Diabète">Diabète</option>
                                            <option value="Bien-être">Bien-être</option>
                                            <option value="Observance">Observance</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-20">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Titre</label>
                                        <input type="text" name="titre" required class="form-control radius-8"
                                            placeholder="Saisir le titre du conseil">
                                    </div>
                                    <div class="col-12 mb-20">
                                        <label
                                            class="form-label fw-semibold text-primary-light text-sm mb-8">Description</label>
                                        <textarea name="description" required class="form-control radius-8" rows="5"
                                            placeholder="Saisir la description détaillée..."></textarea>
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
                                <th scope="col" style="font-size: 13px">Type</th>
                                <th scope="col" style="font-size: 13px">Catégorie</th>
                                <th scope="col" style="font-size: 13px">Titre</th>
                                <th scope="col" style="font-size: 13px">Description</th>
                                <th scope="col" style="font-size: 13px">Date de création</th>
                                <th scope="col" style="font-size: 13px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($conseil as $item)
                                <tr>
                                    <td style="font-size: 13px">
                                        <span
                                            class="bg-primary-focus text-primary-main px-16 py-4 rounded-pill fw-medium text-sm">{{ $item->type }}</span>
                                    </td>
                                    <td style="font-size: 13px"><strong>{{ $item->categorie }}</strong></td>
                                    <td style="font-size: 13px">{{ $item->titre }}</td>
                                    <td style="font-size: 13px">{{ Str::limit($item->description, 50) }}</td>
                                    <td style="font-size: 13px">
                                        {{ \Carbon\Carbon::parse($item->created_at)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)"
                                            class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center me-2"
                                            data-bs-toggle="modal" data-bs-target="#edit{{ $item->id_conseil }}">
                                            <iconify-icon icon="lucide:edit"></iconify-icon>
                                        </a>

                                        <div class="modal fade" id="edit{{ $item->id_conseil }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content radius-16 bg-base">
                                                    <div
                                                        class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0 bg-secondary">
                                                        <h1 class="modal-title fs-5 text-white">Modification du Conseil
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-24">
                                                        <form action="{{ route('conseils.update', $item->id_conseil) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="row">
                                                                <div class="col-6 mb-20">
                                                                    <label
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Type</label>
                                                                    <select required name="type"
                                                                        class="form-control radius-8 form-select">
                                                                        <option value="Conseil"
                                                                            @if ($item->type == 'Conseil') selected @endif>
                                                                            Conseil</option>
                                                                        <option value="Article"
                                                                            @if ($item->type == 'Article') selected @endif>
                                                                            Article</option>
                                                                        <option value="Astuce"
                                                                            @if ($item->type == 'Astuce') selected @endif>
                                                                            Astuce</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-6 mb-20">
                                                                    <label
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Catégorie</label>
                                                                    <input value="{{ $item->categorie }}" type="text"
                                                                        name="categorie" required
                                                                        class="form-control radius-8">
                                                                </div>
                                                                <div class="col-12 mb-20">
                                                                    <label
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Titre</label>
                                                                    <input value="{{ $item->titre }}" type="text"
                                                                        name="titre" required
                                                                        class="form-control radius-8">
                                                                </div>
                                                                <div class="col-12 mb-20">
                                                                    <label
                                                                        class="form-label fw-semibold text-primary-light text-sm mb-8">Description</label>
                                                                    <textarea name="description" required class="form-control radius-8" rows="5">{{ $item->description }}</textarea>
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
                                            data-bs-toggle="modal" data-bs-target="#delete{{ $item->id_conseil }}">
                                            <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                        </a>

                                        <div class="modal fade" id="delete{{ $item->id_conseil }}" tabindex="-1"
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
                                                        <form action="{{ route('conseils.destroy', $item->id_conseil) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="text-center">
                                                                <h6>Êtes-vous sûr de vouloir supprimer ce conseil ?</h6>
                                                                <p class="text-sm text-muted">"{{ $item->titre }}"</p>
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
