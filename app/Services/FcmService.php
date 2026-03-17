<?php

namespace App\Services;

use App\Models\FcmToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
    /**
     * Envoyer une notification à un utilisateur via son username
     */
    public function sendToUser(string $username, string $title, string $body, array $data = []): bool
    {
        $fcm = FcmToken::where('username', $username)
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$fcm || !$fcm->token) {
            Log::info("FCM: pas de token pour $username");
            return false;
        }

        return $this->sendToToken($fcm->token, $title, $body, $data);
    }

    /**
     * Envoyer une notification directement à un token FCM
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        try {
            $messaging = app('firebase.messaging');

            // FCM data doit être string => string
            $stringData = array_map('strval', $data);

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData($stringData);

            $messaging->send($message);
            return true;
        } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
            Log::error("FCM InvalidMessage pour token $token : " . $e->getMessage());
            return false;
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            Log::error("FCM MessagingException pour token $token : " . $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            Log::error("FCM Exception inattendue : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer une notification à plusieurs utilisateurs
     */
    public function sendToMultipleUsers(array $usernames, string $title, string $body, array $data = []): void
    {
        $tokens = FcmToken::whereIn('username', $usernames)
            ->orderBy('updated_at', 'desc')
            ->pluck('token', 'username')
            ->toArray();

        foreach ($tokens as $token) {
            $this->sendToToken($token, $title, $body, $data);
        }
    }
}
