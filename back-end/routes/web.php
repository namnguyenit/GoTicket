<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\AuthController;


Route::get('/', function () {
    return view('welcome');
});

// Dev fallback for serving files from public storage if symlink is missing or blocked
Route::get('/storage/{path}', function ($path) {
    $path = ltrim($path, '/');
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    $full = Storage::disk('public')->path($path);
    $mime = Storage::disk('public')->mimeType($path) ?? 'application/octet-stream';
    $name = basename($path);
    return response()->file($full, [
        'Content-Type' => $mime,
        'Content-Disposition' => 'inline; filename="'.$name.'"',
        'Cache-Control' => 'public, max-age=31536000, immutable',
        'Cross-Origin-Resource-Policy' => 'cross-origin',
        'Access-Control-Allow-Origin' => '*',
    ]);
})->where('path', '.*');

// Route-based public file server to avoid PHP built-in static handling conflicts
Route::get('/files/public/{path}', function ($path) {
    $path = ltrim($path, '/');
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    $full = Storage::disk('public')->path($path);
    $mime = Storage::disk('public')->mimeType($path) ?? 'application/octet-stream';
    $name = basename($path);
    return response()->file($full, [
        'Content-Type' => $mime,
        'Content-Disposition' => 'inline; filename="'.$name.'"',
        'Cache-Control' => 'public, max-age=31536000, immutable',
        'Cross-Origin-Resource-Policy' => 'cross-origin',
        'Access-Control-Allow-Origin' => '*',
    ]);
})->where('path', '.*');
