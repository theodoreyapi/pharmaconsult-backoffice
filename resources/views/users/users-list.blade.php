@extends('layouts.master', ['title' => 'Utilisateurs'])

@push('scripts')
    <script>
        let table = new DataTable('#dataTable');
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
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

        <div class="card h-100 p-0 radius-12">
            <div
                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-3">

                </div>
                <a href="#"
                    class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#addExampleModal">
                    Exporter
                </a>
            </div>

            <div class="card-body p-24">
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                        <thead>
                            <tr>
                                <th scope="col" style="font-size: 13px">Nom</th>
                                <th scope="col" style="font-size: 13px">Contact</th>
                                <th scope="col" style="font-size: 13px">Solde</th>
                                <th scope="col" style="font-size: 13px">Solde avant</th>
                                <th scope="col" style="font-size: 13px">Statut</th>
                                <th scope="col" style="font-size: 13px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patients as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <strong style="font-size: 13px">
                                                {{ $item->first_name }} {!! wordwrap($item->last_name, 20, '<br>') !!}
                                            </strong>
                                        </div>
                                    </td>
                                    <td style="font-size: 13px">
                                        {{ $item->email }}
                                        <br>
                                        {{ $item->phone_number }}
                                    </td>
                                    <td style="font-size: 13px">
                                        {{ $item->amount }}
                                    </td>
                                    <td style="font-size: 13px">
                                        {{ $item->last_amount }}
                                    </td>
                                    <td>
                                        @if ($item->active == 'ACTIVE')
                                            <span
                                                class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>
                                        @else
                                            <span
                                                class="bg-warning-focus text-warning-main px-24 py-4 rounded-pill fw-medium text-sm">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('users.show', $item->id_user) }}"
                                            class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center">
                                            <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                        </a>
                                        <a href="javascript:void(0)"
                                            class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#exampleModal">
                                            <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                        </a>
                                        <div class="modal fade" id="exampleModal" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                                                <div class="modal-content radius-16 bg-base">
                                                    <div
                                                        class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Statut
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-24">
                                                        <form action="#">
                                                            <div class="col-12 mb-20">
                                                                <label for="country"
                                                                    class="form-label fw-semibold text-primary-light text-sm mb-8">Status
                                                                </label>
                                                                <select class="form-control radius-8 form-select"
                                                                    id="country">
                                                                    <option value="">Selectionne</option>
                                                                    <option value="Active">Active</option>
                                                                    <option value="Inactive">Inactive</option>
                                                                </select>
                                                            </div>
                                                            <div
                                                                class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                                                <button type="submit"
                                                                    class="btn btn-primary border border-primary-600 text-md px-50 py-12 radius-8">
                                                                    Enregistrer
                                                                </button>
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
