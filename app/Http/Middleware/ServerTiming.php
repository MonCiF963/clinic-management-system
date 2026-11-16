<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds a Server-Timing header (visible in browser DevTools > Network > Timing) showing how long
 * the app spent in total, how much of it was database time, and how many queries ran.
 */
class ServerTiming
{
    public function handle(Request $request, Closure $next): Response
    {
        $queries = 0;
        $dbMs = 0.0;
        DB::listen(function ($q) use (&$queries, &$dbMs) {
            $queries++;
            $dbMs += $q->time;
        });

        $response = $next($request);

        $start = defined('LARAVEL_START') ? LARAVEL_START : ($request->server('REQUEST_TIME_FLOAT') ?? microtime(true));
        $response->headers->set('Server-Timing', sprintf(
            'app;dur=%.0f, db;dur=%.0f;desc="%d queries"',
            (microtime(true) - $start) * 1000,
            $dbMs,
            $queries
        ));

        return $response;
    }
}
