{{-- resources/views/pharmacies/partials/medicament-list.blade.php --}}
<div class="row">
    @forelse ($medicaments as $item)
        <div class="col-md-5 mb-4">
            <a href="{{ route('medicament.showAllGet', $item->id_medicament) }}">
                <div class="assurance-card d-flex align-items-center gap-2">
                    <img height="50" width="50"
                        src="{{ ($item->medicament_picture ?? URL::asset('assets/images/medicament.jpg')) }}"
                        alt="{{ $item->name }}" class="me-2">
                    <span>
                        <strong>{{ $item->name }}</strong><br>
                        <p style="color: red; margin:0;">{{ $item->price }}</p>
                    </span>
                </div>
            </a>
        </div>

        @if (Auth::user()->role == 'SUPERADMIN')
            <div class="col-md-1 mb-4 d-flex align-items-center">
                <a href="javascript:void(0)"
                    class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center"
                    data-bs-toggle="modal" data-bs-target="#delete{{ $item->id_medicament }}">
                    <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                </a>
            </div>

            <div class="modal fade" id="delete{{ $item->id_medicament }}" tabindex="-1"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content radius-16 bg-base">
                        <div class="modal-header bg-danger py-16 px-24 border border-top-0 border-start-0 border-end-0">
                            <h1 class="modal-title fs-5 text-white">Suppression</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-24">
                            <form action="{{ route('medicament.destroy', $item->id_medicament) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <label>Êtes-vous sûr de vouloir supprimer ce médicament ?</label>
                                <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                    <button type="reset" data-bs-dismiss="modal"
                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                        class="btn btn-danger border border-danger-600 text-md px-50 py-12 radius-8">
                                        Supprimer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @empty
        <p class="text-muted">Aucun médicament trouvé.</p>
    @endforelse
</div>
