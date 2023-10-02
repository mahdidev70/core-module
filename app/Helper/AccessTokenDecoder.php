<?php

namespace TechStudio\Core\app\Helper;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Config;

function isJson($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

class AccessTokenDecoder {
    public static function decode($token) {
        if (config('flags.allow_plain_text_access_token') && isJson($token)) {
            return json_decode($token);
        } else {
            return JWT::decode($token, new Key(Config::get('app.jwt_secret'), 'HS256'));
        }
    }

    public static function encode($object): string
    {
        return JWT::encode($object,config('app.jwt_secret'), 'HS256');
    }
}
