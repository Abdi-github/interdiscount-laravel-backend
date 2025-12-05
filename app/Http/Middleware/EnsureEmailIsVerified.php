<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->is_verified) {
            return $this->error(
                'Your email address is not verified.',
                403,
                'EMAIL_NOT_VERIFIED',
            );
        }

        return $next($request);
    }
}
