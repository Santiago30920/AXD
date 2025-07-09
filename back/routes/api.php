<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\hotel;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TemporateController;
use App\Http\Controllers\ReservationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Routes para el sistema de reservas de hoteles
Route::prefix('hotels')->group(function () {
    // CRUD de hoteles
    Route::get('/', [hotel::class, 'index']); // Obtener todos los hoteles
    Route::post('/', [hotel::class, 'store']); // Crear nuevo hotel
    Route::get('/{id}', [hotel::class, 'show']); // Obtener hotel específico
    Route::put('/{id}', [hotel::class, 'update']); // Actualizar hotel
    Route::delete('/{id}', [hotel::class, 'destroy']); // Eliminar hotel
    
    // Funcionalidades de reservas
    Route::post('/availability', [hotel::class, 'checkAvailability']);
    Route::post('/rates', [hotel::class, 'getRates']);
    Route::post('/calculate', [hotel::class, 'calculateRate']);
    Route::post('/reservation', [hotel::class, 'makeReservation']);
    Route::get('/reservations/all', [hotel::class, 'getReservations']); // Cambié la ruta para evitar conflicto
    Route::get('/room-types/all', [hotel::class, 'getRoomTypes']); // Cambié la ruta para evitar conflicto
});

// API Routes para tipos de habitación (CRUD completo)
Route::prefix('room-types')->group(function () {
    Route::get('/', [RoomTypeController::class, 'index']); // Obtener todos los tipos
    Route::post('/', [RoomTypeController::class, 'store']); // Crear nuevo tipo
    Route::get('/{id}', [RoomTypeController::class, 'show']); // Obtener tipo específico
    Route::put('/{id}', [RoomTypeController::class, 'update']); // Actualizar tipo
    Route::delete('/{id}', [RoomTypeController::class, 'destroy']); // Eliminar tipo
});

// API Routes para habitaciones (CRUD completo)
Route::prefix('rooms')->group(function () {
    Route::get('/', [RoomController::class, 'index']); // Obtener todas las habitaciones
    Route::post('/', [RoomController::class, 'store']); // Crear nueva habitación
    Route::get('/{id}', [RoomController::class, 'show']); // Obtener habitación específica
    Route::put('/{id}', [RoomController::class, 'update']); // Actualizar habitación
    Route::delete('/{id}', [RoomController::class, 'destroy']); // Eliminar habitación
    Route::get('/hotel/{hotel_id}', [RoomController::class, 'getByHotel']); // Habitaciones por hotel
});

// API Routes para temporadas (CRUD completo)
Route::prefix('seasons')->group(function () {
    Route::get('/', [TemporateController::class, 'index']); // Obtener todas las temporadas
    Route::post('/', [TemporateController::class, 'store']); // Crear nueva temporada
    Route::get('/{id}', [TemporateController::class, 'show']); // Obtener temporada específica
    Route::put('/{id}', [TemporateController::class, 'update']); // Actualizar temporada
    Route::delete('/{id}', [TemporateController::class, 'destroy']); // Eliminar temporada
    Route::get('/hotel/{hotel_id}', [TemporateController::class, 'getByHotel']); // Temporadas por hotel
});

// API Routes para reservas (CRUD completo)
Route::prefix('reservations')->group(function () {
    Route::get('/', [ReservationController::class, 'index']); // Obtener todas las reservas
    Route::post('/', [ReservationController::class, 'store']); // Crear nueva reserva
    Route::get('/{id}', [ReservationController::class, 'show']); // Obtener reserva específica
    Route::put('/{id}', [ReservationController::class, 'update']); // Actualizar reserva
    Route::delete('/{id}', [ReservationController::class, 'destroy']); // Cancelar reserva
    Route::get('/hotel/{hotel_id}', [ReservationController::class, 'getByHotel']); // Reservas por hotel
});
