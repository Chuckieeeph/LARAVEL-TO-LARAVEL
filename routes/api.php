<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/status', function () {
    return response()->json([
        'application' => config('app.name'),
        'status' => 'ok',
        'queue' => config('ems.rabbitmq.queue', 'enrollment_queue'),
    ]);
});
