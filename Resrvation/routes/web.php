<?php
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReservationController;

Route::get('/', [RoomController::class, 'calendar']);
Route::get('/api/rooms', [RoomController::class, 'index']);
Route::get('/api/reservations', [ReservationController::class, 'index']);
Route::post('/api/reservations/update', [ReservationController::class, 'update']);
