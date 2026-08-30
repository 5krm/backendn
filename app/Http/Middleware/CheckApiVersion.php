<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponseTrait;

class CheckApiVersion
{
    use ApiResponseTrait;

    /**
     * Minimum required version for the mobile app to function.
     * Could be pulled from config or database.
     */
    private const MIN_REQUIRED_VERSION = '1.0.0';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientVersion = $request->header('X-App-Version');

        if (!$clientVersion) {
            // Optional: Block entirely if missing, or allow with warning. 
            // For now, we enforce it.
            return $this->errorResponse('Missing X-App-Version header. Please update your app.', null, 426);
        }

        if (version_compare($clientVersion, self::MIN_REQUIRED_VERSION, '<')) {
            return $this->errorResponse('App version is too old. Upgrade required.', [
                'current_version' => $clientVersion,
                'required_version' => self::MIN_REQUIRED_VERSION,
            ], 426);
        }

        return $next($request);
    }
}
