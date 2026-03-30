@extends('layouts.master', ['title' => 'QrCode'])

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        let table = new DataTable('#dataTable');
    </script>

    <script>
        async function captureQrCode(pharmacyId) {
            const modalBody = document.querySelector(`#qrcodes${pharmacyId} .modal-body`);
            if (!modalBody) {
                alert("Aucun contenu trouvé à capturer.");
                return;
            }

            // Utilise html2canvas pour capturer le design
            const canvas = await html2canvas(modalBody, {
                scale: 3, // meilleure qualité
                useCORS: true, // pour autoriser les images chargées depuis le serveur
                logging: false,
                backgroundColor: null // préserve la couleur ou le fond défini en CSS
            });

            const imageData = canvas.toDataURL('image/png');

            // Crée le PDF à partir de l’image capturée
            const {
                jsPDF
            } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');

            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();
            const imgWidth = pageWidth - 20;
            const imgHeight = canvas.height * imgWidth / canvas.width;

            pdf.addImage(imageData, 'PNG', 10, 10, imgWidth, imgHeight);

            pdf.save(`qrcode_${pharmacyId}.pdf`);
        }
    </script>
@endpush

@section('content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">QrCode</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ url('index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Tableau de bord
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Liste pharmacien</li>
            </ul>
        </div>

        @include('layouts.statuts')

        <div class="card h-100 p-0 radius-12">
            <div class="card-body p-24">
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                        <thead>
                            <tr>
                                <th scope="col" style="font-size: 13px">Pharmacien</th>
                                <th scope="col" style="font-size: 13px">Pharmacie</th>
                                <th scope="col" style="font-size: 13px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($admins as $item)
                                <tr>
                                    <td>
                                        {{ $item->nomPharmacien }}<br>
                                        {{ $item->username }}
                                    </td>
                                    <td style="font-size: 13px">
                                        {{ $item->nomPharmacy }}
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)"
                                            class="w-32-px h-32-px bg-success-focus text-dark-main rounded-circle d-inline-flex align-items-center justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#qrcodes{{ $item->pharmacyId }}">
                                            <iconify-icon icon="hugeicons:qr-code"></iconify-icon>
                                        </a>

                                        <div class="modal fade" id="qrcodes{{ $item->pharmacyId }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content radius-16 bg-base">
                                                    <div class="modal-header py-16 px-24 border-bottom bg-secondary">
                                                        <h1 class="modal-title fs-5 text-white">QR Code</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body text-center p-4"
                                                        style="background-image: url('{{ URL::asset('assets/images/fond.png') }}');
                                background-size: cover;
                                background-position: center;
                                border-radius: 12px;
                                background-color: #CCFFCB;">

                                                        <div class="p-4">
                                                            <br>
                                                            <br>
                                                            <h5 class="fw-bold mb-0">{{ $item->nomPharmacy }}</h5>
                                                            <p class="fw-bold text-muted mb-3">Dr
                                                                {{ $item->nomPharmacien }}</p>
                                                            <br>
                                                            {{-- Génération du QR code --}}
                                                            <div id="qrcode-container-{{ $item->pharmacyId }}"
                                                                style="background-color: rgba(255,255,255,0.85);
                                    border-radius: 12px;
                                    display: inline-block;
                                    padding: 3%;">
                                                                {!! QrCode::format('svg')->size(250)->generate(\App\Helpers\CryptoHelper::encryptData($item->username)) !!}
                                                            </div>
                                                            <br>
                                                            <br>
                                                            <p class="mt-4 fw-semibold text-dark">Scannez pour payer</p>
                                                            <br>
                                                            <img src="{{ URL::asset('assets/images/PC.png') }}"
                                                                alt="pharma consults" width="120">
                                                            <br><br>
                                                        </div>
                                                    </div>

                                                    <div class="mt-4 d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-outline-danger px-4"
                                                            data-bs-dismiss="modal">Fermer</button>
                                                        <button class="btn btn-success px-4"
                                                            onclick="captureQrCode('{{ $item->pharmacyId }}')">
                                                            Télécharger en PDF
                                                        </button>

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
