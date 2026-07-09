<?php

use App\Http\Controllers\StatementOfAccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/status', function () {
    return response()->json([
        'application' => config('app.name'),
        'status' => 'ok',
        'exchange' => config('rabbitmq.exchange', 'school.events'),
        'queue' => config('rabbitmq.queue', 'accounting.events'),
    ]);
});

Route::get('/students/{student}/statement-of-account', [StatementOfAccountController::class, 'json']);
