<?php

namespace TechStudio\Core\app\Http\Middleware;

use TechStudio\Core\app\Helper\AccessTokenDecoder;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use TechStudio\Core\app\Models\UserProfile;
use Illuminate\Support\Facades\Auth;

class LoginRequired
{
    public function handle(Request $request, Closure $next, $role = null)
    {
        if (!Config::get('flags.community')) {
            throw new AccessDeniedHttpException('flags.community is disabled');
        }
        $token = $request->cookie('sa_access_token') ?? $request->bearerToken('sa_access_token');
        if (!$token) {
            throw new UnauthorizedHttpException('Provide a JWT token.');
        }
        try {
            $decoded = AccessTokenDecoder::decode($token);
        } catch (\DomainException | \UnexpectedValueException | \Firebase\JWT\SignatureInvalidException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }
        $user = UserProfile::where('id', $decoded->user_id)->first();
        if (!$user) {
            throw new AccessDeniedHttpException('The authenticated user does not exist anymore.');
        }
        Auth::setUser($user);
        return $next($request);
    }
}
