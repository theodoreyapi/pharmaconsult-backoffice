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
            <div class="card-body p-24">
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                        <thead>
                            <tr>
                                <th scope="col" style="font-size: 13px">Nom</th>
                                <th scope="col" style="font-size: 13px">Commune</th>
                                <th scope="col" style="font-size: 13px">Responsable</th>
                                <th scope="col" style="font-size: 13px">Garde</th>
                                <th scope="col" style="font-size: 13px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pharmacys as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img height="50" width="50"
                                                src="{{ $item->facade_image ?? URL::asset('assets/images/user-list/user-list1.png') }}"
                                                alt="" class="flex-shrink-0 me-12 radius-8">
                                            <strong style="font-size: 13px">
                                                {!! wordwrap($item->name, 20, '<br>') !!}
                                                <br>
                                                <span style="color: blue">Téléphone : {{ $item->phone_number }}</span>
                                                <br>
                                                <span style="color: green">Whatsapp : {{ $item->whats_app_phone_number }}</span>
                                            </strong>
                                        </div>
                                    </td>
                                    <td style="font-size: 13px">
                                        {!! wordwrap($item->commune, 20, '<br>') !!}
                                    </td>
                                    <td style="font-size: 13px">
                                        {!! wordwrap($item->owner_name, 20, '<br>') !!}
                                    </td>
                                    <td style="font-size: 13px">
                                        du
                                        {{ \Carbon\Carbon::parse($item->start_garde_date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                        <br> au
                                        {{ \Carbon\Carbon::parse($item->end_garde_date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('pharmacy.showAllGet', $item->id_pharmacy) }}"
                                            class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center">
                                            <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                        </a>
                                        @if (Auth::user()->role == 'SUPERADMIN')
                                            <a href="javascript:void(0)"
                                                class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center"
                                                data-bs-toggle="modal" data-bs-target="#delete{{ $item->id_pharmacy }}">
                                                <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                            </a>
                                        @endif
                                        <div class="modal fade" id="delete{{ $item->id_pharmacy }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                                                <div class="modal-content radius-16 bg-base">
                                                    <div
                                                        class="modal-header bg-danger py-16 px-24 border border-top-0 border-start-0 border-end-0">
                                                        <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">
                                                            Suppression
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-24">
                                                        <form action="{{ route('pharmacy.destroy', $item->id_pharmacy) }}"
                                                            method="post" role="form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="row">
                                                                <label for="">Êtes-vous sûr de vouloir
                                                                    supprimer?</label>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                                                    <button type="reset" data-bs-dismiss="modal"
                                                                        aria-label="Close"
                                                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                                                        Annuler
                                                                    </button>
                                                                    <button type="submit"
                                                                        class="btn btn-danger border border-danger-600 text-md px-50 py-12 radius-8">
                                                                        Supprimer
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
