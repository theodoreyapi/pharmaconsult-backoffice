@extends('layouts.master', ['title' => 'Détails utilisateur'])

@section('content')
    <div class="dashboard-main-body">

        {{-- USER HEADER --}}
        <div class="card mb-3 p-3">
            <div class="d-flex align-items-center gap-3">

                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                    style="width:60px;height:60px;">
                    {{ strtoupper(substr($user->first_name, 0, 1)) }}
                </div>

                <div>
                    <h5 class="mb-0">{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <small class="text-muted">{{ $user->email }} | {{ $user->phone_number }}</small>
                </div>

            </div>
        </div>

        {{-- TABS --}}
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#sub">Subscriptions</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#trans">Transferts</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pay">Rechargements</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#app">Rendez-vous</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#profile">Profils santé</a></li>
        </ul>

        <div class="tab-content">

            {{-- ================= SUBSCRIPTIONS ================= --}}
            <div class="tab-pane fade show active" id="sub">

                <form class="mb-2">
                    <select name="status_sub" class="form-control w-25">
                        <option value="">Statut</option>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="expired">Expired</option>
                    </select>
                </form>

                <div class="card p-3">
                    @foreach ($subscriptions as $item)
                        <div class="border-bottom py-2">
                            <b>{{ $item->description }}</b> - {{ $item->status }}
                        </div>
                    @endforeach

                    {{ $subscriptions->links() }}
                </div>

            </div>

            {{-- ================= TRANSFERTS ================= --}}
            <div class="tab-pane fade" id="trans">
                <div class="card p-3">
                    @foreach ($transferts as $item)
                        <div class="border-bottom py-2">
                            {{ $item->type_operation }} - {{ $item->amount }} FCFA
                        </div>
                    @endforeach

                    {{ $transferts->links() }}
                </div>
            </div>

            {{-- ================= RECHARGEMENTS ================= --}}
            <div class="tab-pane fade" id="pay">

                <form class="mb-2">
                    <select name="status_pay" class="form-control w-25">
                        <option value="">Status</option>
                        <option value="success">Success</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </form>

                <div class="card p-3">
                    @foreach ($rechargements as $item)
                        <div class="border-bottom py-2">
                            {{ $item->montant }} FCFA - {{ $item->status }}
                        </div>
                    @endforeach

                    {{ $rechargements->links() }}
                </div>

            </div>

            {{-- ================= APPOINTMENTS ================= --}}
            <div class="tab-pane fade" id="app">

                <form class="mb-2">
                    <select name="status_app" class="form-control w-25">
                        <option value="">Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                </form>

                <div class="card p-3">
                    @foreach ($appointments as $item)
                        <div class="border-bottom py-2">
                            {{ $item->reference }} - {{ $item->status }}
                        </div>
                    @endforeach

                    {{ $appointments->links() }}
                </div>

            </div>

            {{-- ================= HEALTH PROFILES ================= --}}
            <div class="tab-pane fade" id="profile">

                <div class="card p-3">
                    @foreach ($profiles as $profile)
                        <div class="card mb-2 p-3">

                            <h6>{{ $profile->name }}</h6>
                            <small class="text-muted">{{ $profile->profile_type }}</small>

                            <hr>

                            <b>Vaccinations :</b>

                            @if (!empty($profile->vaccinations_list))
                                <ul>
                                    @foreach ($profile->vaccinations_list as $vac)
                                        <li>
                                            {{ $vac->vaccine_name_free ?? 'Vaccin catalogue' }}
                                            - {{ $vac->vaccination_date }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">Aucune vaccination</span>
                            @endif

                        </div>
                    @endforeach

                    {{ $profiles->links() }}
                </div>

            </div>

        </div>
    </div>
@endsection
