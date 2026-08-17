<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\LeaseController;
use App\Http\Controllers\Api\RentScheduleController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('properties', PropertyController::class);
    
    Route::get( 'properties/{property}/units', [UnitController::class, 'index']);
    Route::post(
        'properties/{property}/units',
        [UnitController::class, 'store']
    );
    Route::get(
        'properties/{property}/units/{unit}',
        [UnitController::class, 'show']
    );
     Route::put(
        'properties/{property}/units/{unit}',
        [UnitController::class, 'update']
    );
    Route::delete(
        'properties/{property}/units/{unit}',
        [UnitController::class, 'destroy']
    );
    Route::get('/tenant', [TenantController::class, 'show']);
    Route::put('/tenant', [TenantController::class, 'update']);
    
    Route::get('/leases', [LeaseController::class, 'index']);
    Route::post('/leases', [LeaseController::class, 'store']);
    Route::get('/leases/{id}', [LeaseController::class, 'show']);
    Route::put('/leases/{id}', [LeaseController::class, 'update']);
    Route::delete('/leases/{id}', [LeaseController::class, 'destroy']);

    Route::get('/rent-schedules', [RentScheduleController::class, 'index']);
    Route::post('/rent-schedules', [RentScheduleController::class, 'store']);
    Route::get('/rent-schedules/{id}', [RentScheduleController::class, 'show']);

});