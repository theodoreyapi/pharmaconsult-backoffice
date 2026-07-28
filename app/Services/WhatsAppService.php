<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WhatsAppService
{
    private string $token;
    private string $phoneNumberId;
    private string $apiVersion;

    public function __construct()
    {
        $config = config('services.whatsapp');

        $this->token         = $config['token'];
        $this->phoneNumberId = $config['phone_number_id'];
        $this->apiVersion    = $config['api_version'];
    }

    /**
     * Envoie un message WhatsApp texte libre.
     * Note : hors fenêtre de 24h après le dernier message du patient,
     * seuls des templates pré-approuvés par Meta peuvent être envoyés.
     */
    /**
     * Envoie le message composé par le pharmacien.
     * Essaie d'abord en texte libre (fonctionne si le patient a écrit dans les 24h),
     * sinon bascule automatiquement sur le template approuvé "rappel_patient".
     */
    public function send(string $recipientPhoneNumber, string $message): array
    {
        $recipient = $this->normalizePhoneNumber($recipientPhoneNumber);

        $result = $this->sendFreeText($recipient, $message);

        if (!$result['success'] && $this->isOutsideWindowError($result['error'])) {
            Log::info('WhatsApp: hors fenêtre 24h, bascule sur template', ['recipient' => $recipient]);
            return $this->sendTemplate($recipient, $message);
        }

        return $result;
    }

    private function sendFreeText(string $recipient, string $message): array
    {
        $endpoint = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";

        try {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->post($endpoint, [
                    'messaging_product' => 'whatsapp',
                    'to'                => $recipient,
                    'type'              => 'text',
                    'text'              => ['body' => $message],
                ]);

            if ($response->failed()) {
                Log::warning('WhatsApp texte libre échoué', [
                    'status' => $response->status(),
                    'body'   => $response->json(),
                ]);

                return [
                    'success'     => false,
                    'provider_id' => null,
                    'error'       => $response->body(),
                ];
            }

            $data = $response->json();

            return [
                'success'     => true,
                'provider_id' => $data['messages'][0]['id'] ?? null,
                'error'       => null,
            ];
        } catch (\Throwable $e) {
            return [
                'success'     => false,
                'provider_id' => null,
                'error'       => $e->getMessage(),
            ];
        }
    }

    /**
     * Envoie via le template approuvé "rappel_patient" avec le message du pharmacien
     * comme variable {{1}} du corps du template.
     */
    private function sendTemplate(string $recipient, string $message): array
    {
        $endpoint = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";

        try {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->post($endpoint, [
                    'messaging_product' => 'whatsapp',
                    'to'                => $recipient,
                    'type'              => 'template',
                    'template'          => [
                        'name'     => 'rappel_patient',
                        'language' => ['code' => 'fr'],
                        'components' => [
                            [
                                'type'       => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $message],
                                ],
                            ],
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('WhatsApp template échoué', [
                    'status' => $response->status(),
                    'body'   => $response->json(),
                ]);

                throw new RuntimeException(
                    'WhatsApp API error (' . $response->status() . '): ' . $response->body()
                );
            }

            $data = $response->json();

            return [
                'success'     => true,
                'provider_id' => $data['messages'][0]['id'] ?? null,
                'error'       => null,
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp: échec envoi template', [
                'recipient' => $recipient,
                'error'     => $e->getMessage(),
            ]);

            return [
                'success'     => false,
                'provider_id' => null,
                'error'       => $e->getMessage(),
            ];
        }
    }

    /**
     * Détecte si l'échec est dû à la fenêtre de 24h expirée.
     */
    private function isOutsideWindowError(?string $errorBody): bool
    {
        if (!$errorBody) {
            return false;
        }

        // Codes Meta typiques pour "hors fenêtre 24h" :
        // 131047 (Re-engagement message) / 470 (24h window expired)
        return str_contains($errorBody, '131047')
            || str_contains($errorBody, "24 hours")
            || str_contains($errorBody, 'window');
    }

    // public function send(
    //     string $recipientPhoneNumber,
    //     string $templateName,
    //     string $language = 'en_US'
    // ): array {
    //     $recipient = $this->normalizePhoneNumber($recipientPhoneNumber);

    //     $endpoint = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";

    //     try {

    //         $response = Http::withToken($this->token)
    //             ->acceptJson()
    //             ->post($endpoint, [
    //                 'messaging_product' => 'whatsapp',
    //                 'to' => $recipient,
    //                 'type' => 'template',
    //                 'template' => [
    //                     'name' => 'hello_world',
    //                     'language' => [
    //                         'code' => $language
    //                     ]
    //                 ]
    //             ]);


    //         if ($response->failed()) {

    //             dd([
    //                 'status' => $response->status(),
    //                 'headers' => $response->headers(),
    //                 'body' => $response->body(),
    //                 'json' => $response->json(),
    //             ]);

    //             Log::error('WhatsApp Template Error', [
    //                 'status' => $response->status(),
    //                 'body' => $response->json()
    //             ]);

    //             throw new RuntimeException(
    //                 'WhatsApp API error: ' . $response->body()
    //             );
    //         }


    //         $data = $response->json();

    //         dd([
    //             'status' => $response->status(),
    //             'headers' => $response->headers(),
    //             'body' => $response->body(),
    //             'json' => $response->json(),
    //         ]);

    //         return [
    //             'success' => true,
    //             'provider_id' => $data['messages'][0]['id'] ?? null,
    //             'error' => null,
    //         ];
    //     } catch (\Throwable $e) {

    //         Log::error('WhatsApp Exception', [
    //             'error' => $e->getMessage()
    //         ]);

    //         return [
    //             'success' => false,
    //             'provider_id' => null,
    //             'error' => $e->getMessage()
    //         ];
    //     }
    // }

    private function normalizePhoneNumber(string $number): string
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($number, '00')) {
            $number = substr($number, 2);
        }

        if (!str_starts_with($number, '225') && strlen($number) === 10 && str_starts_with($number, '0')) {
            $number = '225' . $number;
        }

        return $number;
    }
}
