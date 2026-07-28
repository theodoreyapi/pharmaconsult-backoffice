<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Rappel;

class ReminderDispatchService
{
    public function __construct(
        private OrangeSmsService $orangeSms,
        private WhatsAppService $whatsApp
    ) {}

    /**
     * Envoie effectivement le rappel (SMS ou WhatsApp) et met à jour son statut.
     */
    public function send($number, $otp, $firstName)
    {
        return $this->orangeSms->send($number, $otp, $firstName);
    }
}
