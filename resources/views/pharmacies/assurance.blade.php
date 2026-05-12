@extends('layouts.master', ['title' => 'Assurances'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">

        {{-- HEADER --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0">Assurances</h5>
                <small class="text-muted">Gestion des assurances partenaires</small>
            </div>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExampleModal">
                + Ajouter assurance
            </button>
        </div>

        {{-- STATS --}}
        <div class="row mb-3">

            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h4>{{ $assurances->count() }}</h4>
                    <small>Total assurances</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h4>🛡️</h4>
                    <small>Partenaires actifs</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h4>📊</h4>
                    <small>Couverture réseau</small>
                </div>
            </div>

        </div>

        {{-- TABLE --}}
        <div class="card">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle" id="dataTable">

                        <thead class="table-light">
                            <tr>
                                <th>Assurance</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($assurances as $item)
                                <tr>

                                    {{-- IMAGE + NOM --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">

                                            <img src="{{ $item->assurance_picture ?? asset('assets/images/default.png') }}"
                                                width="45" height="45" style="object-fit:cover;border-radius:10px;">

                                            <div>
                                                <b>{{ $item->name }}</b><br>
                                                <small class="text-muted">ID #{{ $item->id_assurance }}</small>
                                            </div>

                                        </div>
                                    </td>

                                    {{-- DESCRIPTION --}}
                                    <td>
                                        {{ Str::limit($item->description, 60) }}
                                    </td>

                                    {{-- STATUS --}}
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td class="d-flex gap-2">

                                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                            data-bs-target="#edit{{ $item->id_assurance }}">
                                            ✏️
                                        </button>

                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#delete{{ $item->id_assurance }}">
                                            🗑
                                        </button>

                                    </td>

                                </tr>

                                {{-- EDIT MODAL --}}
                                <div class="modal fade" id="edit{{ $item->id_assurance }}">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">

                                        <div class="modal-content">

                                            <div class="modal-header bg-primary text-white">
                                                <h5>Modifier assurance</h5>
                                            </div>

                                            <form method="POST"
                                                action="{{ route('assurance.update', $item->id_assurance) }}"
                                                enctype="multipart/form-data">

                                                @csrf
                                                @method('PATCH')

                                                <div class="modal-body">

                                                    <input type="file" name="photo" class="form-control mb-2">
                                                    <br>
                                                    <input type="text" name="libelle" value="{{ $item->name }}"
                                                        class="form-control mb-2" placeholder="Libellé">
                                                    <br>
                                                    <textarea name="description" class="form-control" placeholder="Description">{{ $item->description }}</textarea>

                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        Sauvegarder
                                                    </button>
                                                </div>

                                            </form>

                                        </div>

                                    </div>
                                </div>

                                {{-- DELETE MODAL --}}
                                <div class="modal fade" id="delete{{ $item->id_assurance }}">
                                    <div class="modal-dialog modal-dialog-centered">

                                        <div class="modal-content">

                                            <div class="modal-header bg-danger text-white">
                                                <h5>Suppression</h5>
                                            </div>

                                            <form method="POST"
                                                action="{{ route('assurance.destroy', $item->id_assurance) }}">

                                                @csrf
                                                @method('DELETE')

                                                <div class="modal-body">
                                                    <p>Confirmer la suppression de cette assurance ?</p>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-danger">
                                                        Supprimer
                                                    </button>
                                                </div>

                                            </form>

                                        </div>

                                    </div>
                                </div>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    {{-- ADD MODAL --}}
    <div class="modal fade" id="addExampleModal">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5>Ajouter assurance</h5>
                </div>

                <form action="{{ route('assurance.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="modal-body">

                        <input type="file" name="photo" class="form-control mb-2">
                        <br>
                        <input type="text" name="libelle" class="form-control mb-2" placeholder="Libellé">
                        <br>
                        <input type="text" name="description" class="form-control" placeholder="Description">

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Ajouter</button>
                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
