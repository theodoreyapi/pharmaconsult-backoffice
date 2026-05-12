@extends('layouts.master', ['title' => 'Pharmacies'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Pharmacies</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste des Pharmacies</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="card h-100 p-0 radius-12">
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3">

                </div>
                <a href="{{ url('add-pharmacy') }}"
                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Ajouter une pharmacie
                </a>
            </div>

            <form method="GET" class="row g-2 mb-3">

                {{-- SEARCH --}}
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Rechercher pharmacie, téléphone, commune...">
                </div>

                {{-- COMMUNE --}}
                <div class="col-md-3">
                    <select name="commune" class="form-control">
                        <option value="">Toutes les communes</option>
                        @foreach ($communes as $com)
                            <option value="{{ $com->id_commune }}"
                                {{ request('commune') == $com->id_commune ? 'selected' : '' }}>
                                {{ $com->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- STATUS --}}
                <div class="col-md-2">
                    <select name="status" class="form-control">
                        <option value="">Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- BUTTON --}}
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary w-100">Filtrer</button>

                    <a href="{{ route('pharmacy.index') }}" class="btn btn-light w-100">
                        Reset
                    </a>
                </div>

            </form>

            <div class="card-body p-24">
                <div class="row">

                    @foreach ($pharmacys as $item)
                        <div class="col-md-6 col-lg-4 mb-3">

                            <div class="card shadow-sm border-0 h-100">

                                {{-- IMAGE --}}
                                <img src="{{ $item->facade_image ?? 'https://via.placeholder.com/400x200' }}"
                                    class="card-img-top" style="height:180px; object-fit:cover;">

                                <div class="card-body">

                                    {{-- NAME --}}
                                    <h5 class="mb-1">{{ $item->name }}</h5>
                                    <small class="text-muted">{{ $item->address ?? '' }}</small>
                                    <br>

                                    <span class="badge bg-info mb-2">
                                        {{ $item->commune_name }}
                                    </span>

                                    {{-- RESPONSABLE --}}
                                    <p class="mb-1">
                                        👤 <b>Responsable:</b> {{ $item->owner_name ?? 'N/A' }}
                                    </p>

                                    {{-- GARDE --}}
                                    <p class="mb-1">
                                        🕒 <b>Garde :</b>
                                        {{ \Carbon\Carbon::parse($item->start_garde_date)->translatedFormat('d F Y') }}
                                        →
                                        {{ \Carbon\Carbon::parse($item->end_garde_date)->translatedFormat('d F Y') }}
                                    </p>

                                    {{-- CONTACTS --}}
                                    <p class="mb-1">
                                        📞 {{ $item->phone_number ?? 'N/A' }}
                                    </p>

                                    @if ($item->whats_app_phone_number)
                                        <p class="mb-2">
                                            💬
                                            <a href="https://wa.me/{{ $item->whats_app_phone_number }}" target="_blank">
                                                WhatsApp
                                            </a>
                                        </p>
                                    @endif

                                    {{-- STATUS --}}
                                    @if ($item->is_active == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Inactive</span>
                                    @endif

                                </div>

                                {{-- FOOTER ACTION --}}
                                <div class="card-footer bg-white border-0 d-flex justify-content-between">

                                    <a href="{{ url('view-pharmacy', $item->id_pharmacy) }}"
                                        class="btn btn-sm btn-primary">
                                        Voir détails
                                    </a>

                                    <a href="tel:{{ $item->phone_number }}" class="btn btn-sm btn-outline-secondary">
                                        Appeler
                                    </a>

                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $pharmacys->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
