<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OrangeSmsService
{
    private string $clientId;
    private string $clientSecret;
    private string $countrySender;
    private ?string $senderName;
    private string $tokenUrl;
    private string $baseUrl;

    public function __construct()
    {
        $config = config('services.orange_sms');

        if (!is_array($config)) {
            throw new \RuntimeException(
                "Configuration 'services.orange_sms' introuvable. Vérifie config/services.php et exécute 'php artisan config:clear'."
            );
        }

        $this->clientId      = $config['client_id'] ?? throw new \RuntimeException('ORANGE_SMS_CLIENT_ID manquant dans .env');
        $this->clientSecret  = $config['client_secret'] ?? throw new \RuntimeException('ORANGE_SMS_CLIENT_SECRET manquant dans .env');
        $this->countrySender = $config['country_sender'] ?? 'tel:+2250000';
        $this->senderName    = $config['sender_name'] ?? null;
        $this->tokenUrl      = $config['token_url'] ?? 'https://api.orange.com/oauth/v3/token';
        $this->baseUrl       = $config['base_url'] ?? 'https://api.orange.com/smsmessaging/v1';
    }

    /**
     * Envoie un SMS via l'API Orange.
     * Retourne ['success' => bool, 'provider_id' => string|null, 'error' => string|null]
     */
    public function send(string $recipientPhoneNumber, string $otp, string $firstName): array
    {
        $recipient = $this->normalizePhoneNumber($recipientPhoneNumber);
        $message = "Bonjour $firstName, votre code PharmaConsults est $otp. Valable 2 min. Ne le partagez jamais.";

        try {
            $token = $this->getAccessToken();
            return $this->doSend($token, $recipient, $message, retry: true);
        } catch (\Throwable $e) {
            Log::error('Orange SMS: échec envoi', [
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

    private function doSend(string $token, string $recipient, string $message, bool $retry): array
    {
        $senderAddress = 'tel:+' . $this->countrySender;

        $endpoint = sprintf(
            '%s/outbound/%s/requests',
            $this->baseUrl,
            rawurlencode($senderAddress)
        );

        $body = [
            'outboundSMSMessageRequest' => [
                'address'                => 'tel:+' . $recipient,
                'senderAddress'          => $senderAddress,
                'outboundSMSTextMessage' => [
                    'message' => $message,
                ],
            ],
        ];

        // Sender name personnalisé (si validé par l'équipe locale Orange)
        if (!empty($this->senderName)) {
            $body['outboundSMSMessageRequest']['senderName'] = $this->senderName;
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->contentType('application/json')
            ->post($endpoint, $body);

        // Gestion du token expiré (code 42 / 401) → on régénère une seule fois
        if ($response->status() === 401 && $retry) {
            Cache::forget('orange_sms_access_token');
            $newToken = $this->getAccessToken();
            return $this->doSend($newToken, $recipient, $message, retry: false);
        }

        if ($response->failed()) {
            Log::error('Orange SMS API Error', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'json' => $response->json(),
            ]);

            throw new RuntimeException(
                'Orange SMS API error (' . $response->status() . '): ' . $response->body()
            );
        }

        $data = $response->json();
        $resourceUrl = $data['outboundSMSMessageRequest']['resourceURL'] ?? null;
        $providerId  = $resourceUrl ? basename(parse_url($resourceUrl, PHP_URL_PATH)) : null;

        return [
            'success'     => true,
            'provider_id' => $providerId,
            'error'       => null,
        ];
    }

    /**
     * Récupère (ou génère) un access_token OAuth 2.0 v3, mis en cache 58 minutes.
     */
    private function getAccessToken(): string
    {
        return Cache::remember('orange_sms_access_token', now()->addMinutes(58), function () {
            $authHeader = 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret);

            $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => $authHeader,
                    'Accept'        => 'application/json',
                ])
                ->post($this->tokenUrl, [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->failed()) {
                throw new RuntimeException(
                    'Orange OAuth: impossible de générer le token (' . $response->status() . '): ' . $response->body()
                );
            }

            $data = $response->json();

            if (empty($data['access_token'])) {
                throw new RuntimeException('Orange OAuth: token absent de la réponse.');
            }

            return $data['access_token'];
        });
    }

    /**
     * Normalise le numéro au format attendu (indicatif CI 225, sans + ni 00).
     */
    private function normalizePhoneNumber(string $number): string
    {
        $number = preg_replace('/\D/', '', $number);

        if (str_starts_with($number, '00')) {
            $number = substr($number, 2);
        }

        // Numéro local ivoirien (10 chiffres)
        if (strlen($number) === 10 && !str_starts_with($number, '225')) {
            $number = '225' . $number;
        }

        return $number;
    }
}
