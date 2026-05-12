@extends('layouts.master', ['title' => 'Communes'])

@push('scripts')
<script>
    let table = new DataTable('#dataTable', {
        pageLength: 10,
        responsive: true,
        language: {
            search: "🔎 Rechercher :",
            lengthMenu: "Afficher _MENU_ éléments",
            zeroRecords: "Aucune donnée trouvée",
            info: "Page _PAGE_ sur _PAGES_",
            infoEmpty: "Aucune donnée disponible",
            infoFiltered: "(filtré sur _MAX_ éléments)"
        }
    });
</script>
@endpush

@section('content')
<div class="dashboard-main-body">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold mb-0">Gestion des communes</h5>
            <small class="text-muted">Liste complète des communes enregistrées</small>
        </div>

        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium text-primary">Communes</li>
        </ul>
    </div>

    @include('layouts.statuts')

    {{-- CARD --}}
    <div class="card shadow-sm border-0 radius-12">

        {{-- HEADER ACTION --}}
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">

            <div class="text-muted small">
                📍 Total : <strong>{{ count($communes) }}</strong> communes
            </div>

            <button class="btn btn-primary btn-sm radius-8 d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#addExampleModal">
                <iconify-icon icon="ic:baseline-plus"></iconify-icon>
                Ajouter commune
            </button>

        </div>

        {{-- TABLE --}}
        <div class="card-body p-3">

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable">

                    <thead class="table-light">
                        <tr>
                            <th>Commune</th>
                            <th>Description</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($communes as $item)
                        <tr>

                            {{-- LIBELLE --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div>
                                        <strong>{{ $item->name }}</strong>
                                    </div>
                                </div>
                            </td>

                            {{-- DESCRIPTION --}}
                            <td class="text-muted small">
                                {{ $item->description ?? 'Aucune description' }}
                            </td>

                            {{-- ACTIONS --}}
                            <td>
                                <div class="d-flex gap-2">

                                    {{-- EDIT --}}
                                    <button class="btn btn-sm btn-light text-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#edit{{ $item->id_commune }}">
                                        ✏️
                                    </button>

                                    {{-- DELETE --}}
                                    <button class="btn btn-sm btn-light text-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#delete{{ $item->id_commune }}">
                                        🗑️
                                    </button>

                                </div>
                            </td>

                        </tr>

                        {{-- EDIT MODAL --}}
                        <div class="modal fade" id="edit{{ $item->id_commune }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content radius-12">

                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Modifier commune</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form action="{{ route('commune.update', $item->id_commune) }}" method="post">
                                        @csrf
                                        @method('PATCH')

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label>Libellé</label>
                                                <input type="text" name="libelle" class="form-control"
                                                    value="{{ $item->name }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label>Description</label>
                                                <input type="text" name="description" class="form-control"
                                                    value="{{ $item->description }}">
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
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
                        <div class="modal fade" id="delete{{ $item->id_commune }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content radius-12">

                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Confirmation</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form action="{{ route('commune.destroy', $item->id_commune) }}" method="post">
                                        @csrf
                                        @method('DELETE')

                                        <div class="modal-body text-center">
                                            <p>Voulez-vous vraiment supprimer cette commune ?</p>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
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
<div class="modal fade" id="addExampleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-12">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Ajouter commune</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('commune.store') }}" method="post">
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Libellé</label>
                        <input type="text" name="libelle" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-success">
                        Ajouter
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection
