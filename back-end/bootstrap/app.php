<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate; // <-- THÊM IMPORT NÀY

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'role' => RoleMiddleware::class
        ]);

        // --- START: GHI ĐÈ HÀNH VI REDIRECT CỦA AUTHENTICATE MIDDLEWARE ---
        // Ghi đè phương thức redirectTo cho class Authenticate
        Authenticate::redirectUsing(function (Request $request) {
            // Nếu là API request (expectsJson hoặc bắt đầu bằng api/)
            if ($request->expectsJson() || $request->is('api/*')) {
                // Trả về null để nó ném AuthenticationException thay vì redirect
                return null;
            }
            // Nếu là web request, trả về URL của route tên 'login' (nếu có)
            return route('login'); // Hoặc return '/login'; nếu không dùng tên route
        });
        // --- END: GHI ĐÈ HÀNH VI ---

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Giữ nguyên handler này, nó sẽ xử lý AuthenticationException khi redirectUsing trả về null
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) { // Kiểm tra lại cho chắc chắn
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }
            // Xử lý cho web nếu cần
            // return redirect()->guest(route('login'));
        });


    })
    ->create();