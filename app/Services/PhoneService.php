<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PhoneService
{
    protected $email;
    protected $api_key;

    public function __construct()
    {
        $this->email = config('services.sms_aero.email');
        $this->api_key = config('services.sms_aero.api_key');
    }

    public function sendSMS(string $phone, string $message)
    {
        $authHeader = 'Basic ' . base64_encode($this->email . ':' . $this->api_key);

        $response = Http::withHeaders([
            'Authorization' => $authHeader,
        ])->post('https://gate.smsaero.ru/v2/sms/send', [
            'number' => $phone,
            'sign' => 'SMS Aero',
            'text' => $message,
        ]);
    }
}
