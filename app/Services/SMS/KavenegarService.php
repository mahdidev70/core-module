<?php

namespace TechStudio\Core\app\Services\SMS;

use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\KavenegarApi;

class KavenegarService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('sms.kavenegar.api_key');
    }

    public function send($phoneNumber, $message): string|bool
    {
        try {
            $provider = new KavenegarApi($this->apiKey, true);
            $provider->Send(config('sms.kavenegar.sender'), $phoneNumber, $message);
            return true;
        }
        catch (ApiException|HttpException $e) {
            return $e->errorMessage();
        }
    }

    public function GenerateMessage($user, $message): bool|string
    {
        return self::send($user->username, $message);
    }


}
