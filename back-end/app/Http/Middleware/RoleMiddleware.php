<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiError;

class RoleMiddleware
{

    use ResponseHelper;

    
    public function handle(Request $request, Closure $next, ...$roles): Response
    {

        $user = auth('api')->user();


        if (! $user || ! in_array($user->role, $roles)) {

            return $this->error(ApiError::FORBIDDEN);
        }

        return $next($request);
    }
}
