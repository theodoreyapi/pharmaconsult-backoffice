<?php

namespace App\Helpers;

class CryptoHelper
{
    private static $key = '12345678901234567890123456789012'; // 32 chars
    private static $iv = '1234567890123456'; // 16 chars

    public static function encryptData($data)
    {
        // openssl_encrypt retourne déjà du base64 avec l'option 0
        $encrypted = openssl_encrypt(
            $data,
            'AES-256-CBC',
            self::$key,
            0, // Retourne base64 directement
            self::$iv
        );

        return $encrypted; // Pas de base64_encode() supplémentaire !
    }
}
