@extends('layouts.master', ['title' => 'Utilisateurs'])

@section('content')
    <div class="dashboard-main-body">

        {{-- HEADER --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Utilisateurs</h6>

            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste utilisateur</li>
            </ul>
        </div>

        {{-- CARD --}}
        <div class="card h-100 radius-12">

            {{-- HEADER CARD --}}
            <div class="card-header bg-base py-16 px-24 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Liste des utilisateurs</h6>

                <a href="#" class="btn btn-primary btn-sm radius-8">
                    Exporter
                </a>
            </div>

            <div class="card-body p-24">

                <form method="GET" class="row g-2 mb-3">

                    {{-- SEARCH --}}
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Rechercher nom, email, téléphone...">
                    </div>

                    {{-- STATUS --}}
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">Statut</option>
                            <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                            <option value="INACTIVE" {{ request('status') == 'INACTIVE' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>

                    {{-- MIN SOLDE --}}
                    <div class="col-md-2">
                        <input type="number" name="min" value="{{ request('min') }}" class="form-control"
                            placeholder="Solde min">
                    </div>

                    {{-- MAX SOLDE --}}
                    <div class="col-md-2">
                        <input type="number" name="max" value="{{ request('max') }}" class="form-control"
                            placeholder="Solde max">
                    </div>

                    {{-- BUTTON --}}
                    <div class="col-md-2 d-flex gap-2">
                        <button class="btn btn-primary w-100">
                            Filtrer
                        </button>

                        <a href="{{ route('users.index') }}" class="btn btn-light w-100">
                            Reset
                        </a>
                    </div>

                </form>

                <div class="mb-2 text-muted">
                    {{ $patients->total() }} utilisateur(s) trouvé(s)
                </div>

                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Utilisateur</th>
                                <th>Contact</th>
                                <th>Solde</th>
                                <th>Ancien solde</th>
                                <th>Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($patients as $item)
                                @php
                                    $modalId = 'deleteModal-' . $item->id_user;
                                @endphp

                                <tr>

                                    {{-- USER --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                style="width:40px;height:40px;">
                                                {{ strtoupper(substr($item->first_name, 0, 1)) }}
                                            </div>

                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $item->first_name }} {{ $item->last_name }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ $item->username }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- CONTACT --}}
                                    <td>
                                        <div>{{ $item->email }}</div>
                                        <small class="text-muted">{{ $item->phone_number }}</small>
                                    </td>

                                    {{-- SOLDE --}}
                                    <td class="text-success fw-semibold">
                                        {{ number_format($item->amount, 0, ',', ' ') }} FCFA
                                    </td>

                                    {{-- OLD --}}
                                    <td>
                                        {{ number_format($item->last_amount, 0, ',', ' ') }} FCFA
                                    </td>

                                    {{-- STATUS --}}
                                    <td>
                                        @if ($item->active == 'ACTIVE')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Inactive</span>
                                        @endif
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td class="text-center">
                                        <a href="{{ route('users.show', $item->id_user) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Voir
                                        </a>

                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#{{ $modalId }}">
                                            Supprimer
                                        </button>
                                    </td>

                                </tr>

                                {{-- MODAL DELETE --}}
                                <div class="modal fade" id="{{ $modalId }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                Voulez-vous vraiment supprimer :
                                                <b>{{ $item->first_name }} {{ $item->last_name }}</b> ?
                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Annuler
                                                </button>

                                                <form method="POST" action="{{ route('users.destroy', $item->id_user) }}">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="btn btn-danger">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="mt-3 d-flex justify-content-center">
                    {{ $patients->links() }}
                </div>

            </div>
        </div>

    </div>
@endsection
