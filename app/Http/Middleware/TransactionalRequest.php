<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TransactionalRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        DB::beginTransaction();

        try {
            $response = $next($request);

            if (
                // Commit the transaction if the response is NOT a client error (4xx) or server error (5xx).
                // This includes redirects (3xx) and successful responses (2xx).
                !$response->isClientError() && !$response->isServerError()
            ) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            return $response;

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}