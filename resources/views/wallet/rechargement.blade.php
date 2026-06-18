@extends('layouts.master', ['title' => 'Rechargement de portefeuille'])

@section('content')
    <div class="dashboard-main-body">

        {{-- HEADER --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Rechargement de portefeuille</h6>

            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ url('index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste utilisateur</li>
            </ul>
        </div>

        @include('layouts.statuts')

        {{-- CARD --}}
        <div class="card h-100 radius-12">

            <div class="card-body p-24">

                <form method="GET" class="row g-3 mb-4">

                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Username ou transaction">
                    </div>

                    <div class="col-md-2">
                        <select name="payment_method" class="form-select">

                            <option value="">
                                Toutes méthodes
                            </option>

                            <option value="wave" {{ request('payment_method') == 'wave' ? 'selected' : '' }}>
                                Wave
                            </option>

                            <option value="orange" {{ request('payment_method') == 'orange' ? 'selected' : '' }}>
                                Orange Money
                            </option>

                            <option value="mtn" {{ request('payment_method') == 'mtn' ? 'selected' : '' }}>
                                MTN Money
                            </option>

                            <option value="moov" {{ request('payment_method') == 'moov' ? 'selected' : '' }}>
                                Moov Money
                            </option>

                        </select>
                    </div>

                    <div class="col-md-2">
                        <input type="number" name="min" class="form-control" value="{{ request('min') }}"
                            placeholder="Montant min">
                    </div>

                    <div class="col-md-2">
                        <input type="number" name="max" class="form-control" value="{{ request('max') }}"
                            placeholder="Montant max">
                    </div>

                    <div class="col-md-2 d-flex gap-2">

                        <button class="btn btn-primary w-100">
                            Filtrer
                        </button>

                        <a href="{{ route('rechargements.index') }}" class="btn btn-light w-100">
                            Reset
                        </a>

                    </div>

                </form>

                <div class="mb-3 text-muted">
                    {{ $rechargements->total() }}
                    rechargement(s) en attente
                </div>

                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        <thead>
                            <tr>
                                <th>Transaction</th>
                                <th>Utilisateur</th>
                                <th>Méthode</th>
                                <th>Montant</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($rechargements as $item)
                                <tr>

                                    <td>
                                        {{ $item->transaction_id ?? '-' }}
                                    </td>

                                    <td>
                                        <strong>{{ $item->first_name }} {{ $item->last_name }}</strong>
                                        <br>
                                        {{ $item->username }}
                                    </td>

                                    <td>

                                        @switch($item->payment_method)
                                            @case('wave')
                                                <span class="badge bg-info">Wave</span>
                                            @break

                                            @case('orange')
                                                <span class="badge bg-warning">
                                                    Orange Money
                                                </span>
                                            @break

                                            @case('mtn')
                                                <span class="badge bg-primary">
                                                    MTN Money
                                                </span>
                                            @break

                                            @case('moov')
                                                <span class="badge bg-success">
                                                    Moov Money
                                                </span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">
                                                    {{ $item->payment_method }}
                                                </span>
                                        @endswitch

                                    </td>

                                    <td class="fw-bold text-success">
                                        {{ number_format($item->montant, 0, ',', ' ') }}
                                        FCFA
                                    </td>

                                    <td>
                                        {{ $item->created_at->format('d/m/Y H:i') }}
                                    </td>

                                    <td>

                                        <div class="d-flex gap-2">

                                            <form method="POST"
                                                action="{{ route('rechargements.valider', $item->id_rechargement) }}"
                                                onsubmit="return confirm('Valider ce rechargement ?')">

                                                @csrf

                                                <button class="btn btn-success btn-sm">
                                                    <i class="ri-check-line"></i>
                                                    Valider
                                                </button>

                                            </form>

                                            <form method="POST"
                                                action="{{ route('rechargements.destroy', $item->id_rechargement) }}"
                                                onsubmit="return confirm('Supprimer ce rechargement ?')">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-danger btn-sm">
                                                    <i class="ri-delete-bin-line"></i>
                                                    Supprimer
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            Aucun rechargement en attente.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $rechargements->links() }}
                    </div>

                </div>
            </div>

        </div>
    @endsection
