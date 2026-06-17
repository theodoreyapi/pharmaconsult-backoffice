@extends('layouts.master', ['title' => 'Détails Utilisateur'])

@section('content')
    <div class="dashboard-main-body" style="background-color: #f8fafc; padding: 24px;">

        {{-- ─── USER PROFILE CARD ─── --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-4">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                        style="width: 72px; height: 72px; font-size: 24px; background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                        {{ strtoupper(substr($user->first_name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1">{{ $user->first_name }} {{ $user->last_name }}</h4>
                        <div class="d-flex flex-wrap gap-3 text-muted small">
                            <span><i class="ph ph-envelope me-1"></i> {{ $user->email }}</span>
                            <span>|</span>
                            <span><i class="ph ph-phone me-1"></i> {{ $user->phone_number }}</span>
                            <span>|</span>
                            <span class="badge bg-light text-primary border border-primary-subtle px-2 py-1">ID:
                                #{{ $user->id_user }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── NAVIGATION TABS ─── --}}
        <ul class="nav nav-pills p-2 bg-white rounded-3 shadow-sm mb-4 gap-2" id="profileTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active rounded-2 fw-semibold" data-bs-toggle="tab" href="#sub"><i
                        class="ph ph-credit-card me-1"></i> Abonnements</a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-2 fw-semibold" data-bs-toggle="tab" href="#trans"><i
                        class="ph ph-arrows-left-right me-1"></i> Transferts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-2 fw-semibold" data-bs-toggle="tab" href="#pay"><i
                        class="ph ph-wallet me-1"></i> Rechargements</a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-2 fw-semibold" data-bs-toggle="tab" href="#app"><i
                        class="ph ph-calendar me-1"></i> Rendez-vous</a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-2 fw-semibold" data-bs-toggle="tab" href="#profile"><i
                        class="ph ph-heart-beat me-1"></i> Profils Santé</a>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ══ TAB: SUBSCRIPTIONS ══ --}}
            <div class="tab-pane fade show active" id="sub">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">Historique des abonnements</h5>
                    <form method="GET" class="d-flex gap-2">
                        <select name="status_sub" class="form-select form-select-sm" onchange="this.form.submit()"
                            style="width: 160px;">
                            <option value="">Tous les statuts</option>
                            <option value="paid" {{ request('status_sub') == 'paid' ? 'selected' : '' }}>Payé</option>
                            <option value="pending" {{ request('status_sub') == 'pending' ? 'selected' : '' }}>En attente
                            </option>
                            <option value="expired" {{ request('status_sub') == 'expired' ? 'selected' : '' }}>Expiré
                            </option>
                        </select>
                    </form>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small uppercase">
                                <tr>
                                    <th class="ps-4">Module / Description</th>
                                    <th>Cycle</th>
                                    <th>Valide jusqu'au</th>
                                    <th class="text-end pe-4">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subscriptions as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $item->module_libelle ?? 'Module inconnu' }}
                                            </div>
                                            <small class="text-muted">{{ $item->description }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-secondary">Annuel</span></td>
                                        <td>{{ $item->valid_until ? \Carbon\Carbon::parse($item->valid_until)->format('d/m/Y H:i') : 'N/A' }}
                                        </td>
                                        <td class="text-end pe-4">
                                            <span
                                                class="badge px-2.5 py-1.5 rounded-pill {{ $item->status === 'paid' || $item->status === 'ACTIF' ? 'bg-success-subtle text-success' : ($item->status === 'pending' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }}">
                                                {{ strtoupper($item->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Aucun abonnement trouvé.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-0 p-3">
                        {{ $subscriptions->appends(request()->query())->links() }}</div>
                </div>
            </div>

            {{-- ══ TAB: TRANSFERTS ══ --}}
            <div class="tab-pane fade" id="trans">
                <h5 class="fw-bold text-dark mb-3">Historique des Transferts</h5>
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4">Type Opération</th>
                                    <th>Détails Flux</th>
                                    <th>Type Contexte</th>
                                    <th class="text-end pe-4">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transferts as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <span
                                                class="badge {{ $item->type_operation === 'CREDIT' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-2 py-1">
                                                {{ $item->type_operation }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="d-block"><b>De:</b>
                                                {{ $item->sender_username ?? 'Système' }}</small>
                                            <small class="d-block text-muted"><b>À:</b>
                                                {{ $item->receiver_username }}</small>
                                        </td>
                                        <td><span class="text-muted small">{{ str_replace('_', ' ', $item->type) }}</span>
                                        </td>
                                        <td
                                            class="text-end pe-4 fw-bold {{ $item->type_operation === 'CREDIT' ? 'text-success' : 'text-dark' }}">
                                            {{ number_format($item->amount, 0, '.', ' ') }} FCFA
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Aucun transfert enregistré.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-0 p-3">{{ $transferts->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

            {{-- ══ TAB: RECHARGEMENTS ══ --}}
            <div class="tab-pane fade" id="pay">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">Comptes & Rechargements</h5>
                    <form method="GET">
                        <select name="status_pay" class="form-select form-select-sm" onchange="this.form.submit()"
                            style="width: 160px;">
                            <option value="">Tous les statuts</option>
                            <option value="success" {{ request('status_pay') == 'success' ? 'selected' : '' }}>Succès
                            </option>
                            <option value="pending" {{ request('status_pay') == 'pending' ? 'selected' : '' }}>En attente
                            </option>
                            <option value="failed" {{ request('status_pay') == 'failed' ? 'selected' : '' }}>Échoué
                            </option>
                        </select>
                    </form>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4">ID Transaction</th>
                                    <th>Méthode</th>
                                    <th>Montant</th>
                                    <th class="text-end pe-4">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rechargements as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <span
                                                class="fw-mono text-secondary small">{{ $item->transaction_id ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border text-capitalize px-2 py-1">
                                                {{ $item->payment_method }}
                                            </span>
                                        </td>
                                        <td class="fw-bold">{{ number_format($item->montant, 0, '.', ' ') }}
                                            {{ $item->currency }}</td>
                                        <td class="text-end pe-4">
                                            <span
                                                class="badge px-2.5 py-1.5 rounded-pill {{ $item->status === 'success' ? 'bg-success-subtle text-success' : ($item->status === 'pending' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }}">
                                                {{ strtoupper($item->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Aucun rechargement trouvé.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-0 p-3">
                        {{ $rechargements->appends(request()->query())->links() }}</div>
                </div>
            </div>

            {{-- ══ TAB: RENDEZ-VOUS ══ --}}
            <div class="tab-pane fade" id="app">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">Rendez-vous médicaux</h5>
                    <form method="GET">
                        <select name="status_app" class="form-select form-select-sm" onchange="this.form.submit()"
                            style="width: 160px;">
                            <option value="">Tous les états</option>
                            <option value="pending" {{ request('status_app') == 'pending' ? 'selected' : '' }}>En attente
                            </option>
                            <option value="confirmed" {{ request('status_app') == 'confirmed' ? 'selected' : '' }}>
                                Confirmé</option>
                            <option value="cancelled" {{ request('status_app') == 'cancelled' ? 'selected' : '' }}>Annulé
                            </option>
                            <option value="completed" {{ request('status_app') == 'completed' ? 'selected' : '' }}>Terminé
                            </option>
                        </select>
                    </form>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4">Référence</th>
                                    <th>Patient / Vaccin</th>
                                    <th>Pharmacie / Date</th>
                                    <th class="text-end pe-4">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($appointments as $item)
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary small">{{ $item->reference }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->patient_name ?? 'Non spécifié' }}
                                            </div>
                                            <small class="text-success"><i class="ph ph-syringe"></i>
                                                {{ $item->vaccine_name ?? 'Vaccin général' }}</small>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-dark">
                                                {{ $item->pharmacy_name ?? 'Centre Pharma' }}</div>
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($item->appointment_date)->format('d F Y') }}</small>
                                        </td>
                                        <td class="text-end pe-4">
                                            @php
                                                $appStatuses = [
                                                    'pending' => 'bg-warning-subtle text-warning',
                                                    'confirmed' => 'bg-info-subtle text-info',
                                                    'completed' => 'bg-success-subtle text-success',
                                                    'cancelled' => 'bg-danger-subtle text-danger',
                                                ];
                                            @endphp
                                            <span
                                                class="badge px-2.5 py-1.5 rounded-pill {{ $appStatuses[$item->status] ?? 'bg-secondary-subtle' }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Aucun rendez-vous planifié.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-0 p-3">
                        {{ $appointments->appends(request()->query())->links() }}</div>
                </div>
            </div>

            {{-- ══ TAB: HEALTH PROFILES ══ --}}
            <div class="tab-pane fade" id="profile">
                <h5 class="fw-bold text-dark mb-3">Profils Santé Rattachés</h5>

                <div class="row g-3">
                    @forelse ($profiles as $profile)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100"
                                style="border-radius: 12px; border-left: 4px solid {{ $profile->profile_type === 'human' ? '#3b82f6' : '#10b981' }} !important;">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-0">{{ $profile->name }}</h5>
                                            <span
                                                class="badge mt-1 {{ $profile->profile_type === 'human' ? 'bg-blue-subtle text-primary' : 'bg-emerald-subtle text-success' }} text-capitalize">
                                                {{ $profile->profile_type }}
                                                {{ $profile->relation ? '(' . $profile->relation . ')' : '' }}
                                            </span>
                                        </div>
                                        <span class="text-muted small"><i class="ph ph-gender-intersex"></i>
                                            {{ ucfirst($profile->gender ?? 'Non spécifié') }}</span>
                                    </div>

                                    <div class="text-muted small mb-3">
                                        <i class="ph ph-cake"></i> Né(e) le :
                                        <b>{{ $profile->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->format('d/m/Y') : 'Inconnu' }}</b>
                                    </div>

                                    <div class="bg-light p-3 rounded-3">
                                        <div class="fw-bold text-secondary small mb-2"><i
                                                class="ph ph-shield-check text-success"></i> Suivi des Vaccinations :</div>
                                        @if ($profile->vaccinations_list->isNotEmpty())
                                            <ul class="list-unstyled mb-0 small">
                                                @foreach ($profile->vaccinations_list as $vac)
                                                    <li class="mb-1 d-flex justify-content-between">
                                                        <span class="text-dark fw-semibold">•
                                                            {{ $vac->vaccine_name_free ?? 'Vaccin standard' }}</span>
                                                        <span
                                                            class="text-muted text-end">{{ \Carbon\Carbon::parse($vac->vaccination_date)->format('d/m/Y') }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted italic small">Aucun historique de vaccination
                                                enregistré.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-muted bg-white rounded-3 shadow-sm card">Aucun profil de
                            santé disponible.</div>
                    @endforelse
                </div>
                <div class="mt-3">{{ $profiles->appends(request()->query())->links() }}</div>
            </div>

        </div>
    </div>
@endsection
