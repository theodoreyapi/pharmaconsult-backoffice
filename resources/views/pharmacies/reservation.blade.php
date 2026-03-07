@extends('layouts.master', ['title' => 'Reservation'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Reservation</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste des reservations</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="card h-100 p-0 radius-12">
            {{-- <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                        <option>Status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>
            </div> --}}
            <div class="card-body p-24">
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                        <thead>
                            <tr>
                                <th scope="col">Médicament</th>
                                <th scope="col">Date de Demande</th>
                                <th scope="col">Patient</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requetes as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item['medicament']['name'] }}</strong>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item['dateCreate'])->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                    </td>
                                    <td>{{ $item['firstAndLastName'] }}</td>
                                    <td>
                                        @if ($item['status'] == 'VALIDE')
                                            <span
                                                class="bg-primary text-white px-24 py-4 rounded-pill fw-medium text-sm">VALIDE</span>
                                        @elseif ($item['status'] == 'EN_ATTENTE')
                                            <span
                                                class="bg-warning text-white px-24 py-4 rounded-pill fw-medium text-sm">ATTENTE</span>
                                        @else
                                            <span
                                                class="bg-success text-white px-24 py-4 rounded-pill fw-medium text-sm">RESERVE</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item['status'] == 'EN_ATTENTE')
                                            <a href="javascript:void(0)"
                                                class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center"
                                                data-bs-toggle="modal" data-bs-target="#edit{{ $item['requestId'] }}">
                                                <iconify-icon icon="lucide:check"></iconify-icon>
                                            </a>
                                            <div class="modal fade" id="edit{{ $item['requestId'] }}" tabindex="-1"
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
                                                            <form action="{{ route('requete.update', $item['requestId']) }}"
                                                                method="post" role="form"
                                                                enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PATCH')
                                                                <div class="row">
                                                                    <div class="col-12 mb-20">
                                                                        <label for="country"
                                                                            class="form-label fw-semibold text-primary-light text-sm mb-8">Statut
                                                                        </label>
                                                                        <select required name="statut"
                                                                            class="form-control radius-8 form-select"
                                                                            id="country">
                                                                            <option value="">Sélectionne
                                                                            </option>
                                                                            <option value="true">DISPONIBLE</option>
                                                                            <option value="false">INDISPONIBLE</option>
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
                                                                            Valider
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
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
