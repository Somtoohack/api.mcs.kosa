<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckJsonHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Allow requests to a specific route without checks
        if ($request->is('redbiller/webhook/*')) {
            return $next($request);
        }

        if (
            $request->header('Accept') !== 'application/json' ||
            $request->header('Content-Type') !== 'application/json'
        ) {
            return response()->json(
                [
                    'success' => false,
                    'code'    => 4000,
                    'message' => 'Request denied',
                    'error'   => 'Invalid request pattern.',
                ],
                200
            );
        }
        if (
            trim($request->header('KOSA-MCS-KEY')) !== trim(env('KOSA_MCS_KEY')) ||
            trim($request->header('KOSA-CORE-KEY')) !== trim(env('KOSA_CORE_KEY'))
        ) {
            return response()->json(
                [
                    'success' => false,
                    'code'    => 4000,
                    'message' => 'Request denied',
                    'error'   => 'Unidentified Source or Target.',
                ],
                200
            );
        }

        Log::info('Request Details:', [
            'attributes' => $request->attributes->all(),
            'request'    => $request->all(),
            'query'      => $request->query->all(),
            'server'     => $request->server->all(),
            'files'      => $request->files->all(),
            'cookies'    => $request->cookies->all(),
            'headers'    => $request->headers->all(),
        ]);

        return $next($request);
    }
}
