@extends('layouts.master', ['title' => 'Demande'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Demande</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste des demandes</li>
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
                                            <form action="{{ route('requete.update', $item['requestId']) }}" method="post"
                                                role="form">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" required name="statut" id="" value="true"
                                                    hidden classz="form-control radius-8">
                                                <button type="submit" title="Disponible"
                                                    class="badge text-sm fw-r text-success-600 bg-success-100 px-20 py-9 radius-4 text-white"
                                                    data-bs-toggle="modal" data-bs-target="#edit{{ $item['requestId'] }}">DISPONIBLE
                                                </button>
                                            </form>
                                            <br>
                                            <form action="{{ route('requete.update', $item['requestId']) }}" method="post"
                                                role="form">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" required name="statut" id="" value="false"
                                                    hidden classz="form-control radius-8">
                                                <button type="submit" title="Non disponible"
                                                    class="badge text-sm fw-r text-danger-600 bg-danger-100 px-20 py-9 radius-4 text-white"
                                                    data-bs-toggle="modal" data-bs-target="#edit{{ $item['requestId'] }}">
                                                    NON DISPONIBLE
                                                </button>
                                            </form>
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
