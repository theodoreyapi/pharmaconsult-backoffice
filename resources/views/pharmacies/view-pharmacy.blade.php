@extends('layouts.master', ['title' => 'Détails pharmacie'])

@section('content')
    <div class="dashboard-main-body">

        {{-- HEADER --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Détails pharmacie</h6>
        </div>

        {{-- ACTIONS --}}
        <div class="card-header bg-base py-16 px-24 d-flex justify-content-between align-items-center">
            <div></div>

            <div class="d-flex gap-2">
                <a href="{{ route('pharmacy.show', $pharmacys->id_pharmacy) }}" class="btn btn-info btn-sm">
                    Associer assurance
                </a>

                <a href="{{ route('pharmacy.edit', $pharmacys->id_pharmacy) }}" class="btn btn-success btn-sm">
                    Associer paiement
                </a>
            </div>
        </div>

        <br>

        {{-- STATS --}}
        <div class="row mb-3">

            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <h5>{{ $average }}/5</h5>
                    <small>Note moyenne</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <h5>{{ $counter }}</h5>
                    <small>Avis clients</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <h5>{{ $paymentMethods->count() }}</h5>
                    <small>Paiements</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <h5>{{ $assurances->count() }}</h5>
                    <small>Assurances</small>
                </div>
            </div>

        </div>

        <div class="row gy-4">

            {{-- LEFT CARD --}}
            <div class="col-lg-5">

                <div class="card overflow-hidden">

                    <img src="{{ $pharmacys->facade_image ?? asset('assets/images/pharmacy.jpg') }}" class="w-100"
                        style="height:200px; object-fit:cover;">

                    <div class="card-body">

                        <h5>{{ $pharmacys->name }}</h5>
                        <p class="text-muted">{{ $pharmacys->address }}</p>

                        <hr>

                        <p>👤 <b>Responsable:</b> {{ $pharmacys->owner_name }}</p>
                        <p>📞 {{ $pharmacys->phone_number }}</p>
                        <p>💬 {{ $pharmacys->whats_app_phone_number }}</p>
                        <p>📍 {{ $pharmacys->commune_name }}</p>

                        <p>🕒 Ouverture: {{ $pharmacys->opening_hours }}</p>

                        {{-- GARDE --}}
                        @php
                            $start = \Carbon\Carbon::parse($pharmacys->start_garde_date);
                            $end = \Carbon\Carbon::parse($pharmacys->end_garde_date);
                        @endphp

                        <p>
                            🕒 Garde :
                            {{ $start->format('d M Y') }} → {{ $end->format('d M Y') }}

                            @if (now()->between($start, $end))
                                <span class="badge bg-success">En cours</span>
                            @else
                                <span class="badge bg-secondary">Hors garde</span>
                            @endif
                        </p>

                    </div>
                </div>

            </div>

            {{-- RIGHT TABS --}}
            <div class="col-lg-7">

                <div class="card">
                    <div class="card-body">

                        <ul class="nav nav-tabs mb-3">

                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#paiement">
                                    Paiements & Assurances
                                </button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">
                                    Avis clients
                                </button>
                            </li>

                        </ul>

                        <div class="tab-content">

                            {{-- PAIEMENTS --}}
                            <div class="tab-pane fade show active" id="paiement">

                                <h6>Méthodes de paiement</h6>

                                <div class="row">
                                    @foreach ($paymentMethods as $method)
                                        <div class="col-6 mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $method->payment_method_picture }}" width="30">
                                                {{ $method->name }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <hr>

                                <h6>Assurances</h6>

                                <div class="row">
                                    @foreach ($assurances as $assurance)
                                        <div class="col-6 mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $assurance->assurance_picture }}" width="30">
                                                {{ $assurance->name }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>

                            {{-- REVIEWS --}}
                            <div class="tab-pane fade" id="reviews">

                                {{-- RATING SUMMARY --}}
                                <div class="text-center mb-3">
                                    <h3>{{ $average }}</h3>
                                    <small>sur 5</small>
                                </div>

                                {{-- LIST REVIEWS --}}
                                @foreach ($reviews as $review)
                                    <div class="card mb-2 p-3">

                                        <div class="d-flex justify-content-between">

                                            <b>{{ $review->userName }}</b>

                                            <small>
                                                {{ \Carbon\Carbon::parse($review->dateNotice)->diffForHumans() }}
                                            </small>

                                        </div>

                                        <div>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span style="color: {{ $i <= $review->note ? '#41BA3E' : '#ccc' }}">
                                                    ★
                                                </span>
                                            @endfor
                                        </div>

                                        <p class="mt-2">{{ $review->details }}</p>

                                    </div>
                                @endforeach

                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

        <br>

        <div class="card h-100 p-24 radius-12">

            <h5 class="mb-3">Modifier la pharmacie</h5>

            <form action="{{ route('pharmacy.update', $pharmacys->id_pharmacy) }}" method="POST"
                enctype="multipart/form-data">

                @csrf
                @method('PATCH')

                <div class="row">

                    {{-- IMAGE --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Photo pharmacie</label>
                        <input type="file" name="photo" class="form-control">
                    </div>

                    {{-- NOM --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="name" value="{{ $pharmacys->name }}" class="form-control" required>
                    </div>

                    {{-- RESPONSABLE --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Responsable</label>
                        <input type="text" name="responsable" value="{{ $pharmacys->owner_name }}" class="form-control">
                    </div>

                    {{-- ADRESSE --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Adresse</label>
                        <input type="text" name="adresse" value="{{ $pharmacys->address }}" class="form-control">
                    </div>

                    {{-- COMMUNE --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Commune</label>
                        <select name="commune" required class="form-control">

                            <option value="{{ $pharmacys->commune_id }}">
                                {{ $pharmacys->commune_name }}
                            </option>

                            @foreach ($communes as $item)
                                <option value="{{ $item->id_commune }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- TELEPHONE --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="phone" value="{{ $pharmacys->phone_number }}" class="form-control">
                    </div>

                    {{-- WHATSAPP --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ $pharmacys->whats_app_phone_number }}"
                            class="form-control">
                    </div>

                    {{-- GPS --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">GPS (lien / coordonnées)</label>
                        <input type="text" name="longitude" value="{{ $pharmacys->gps_coordinates }}"
                            class="form-control">
                    </div>

                </div>

                {{-- BUTTONS --}}
                <div class="d-flex justify-content-end gap-2 mt-3">

                    <button type="reset" class="btn btn-light">
                        Annuler
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Enregistrer les modifications
                    </button>

                </div>

            </form>

        </div>

    </div>
@endsection
