<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\hotelModel;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Exception;

class ReservationController extends Controller
{
    /**
     * Obtener todas las reservas
     */
    public function index()
    {
        try {
            $reservations = Reservation::with(['hotel', 'room.roomType', 'roomType'])->get();
            return response()->json([
                'success' => true,
                'data' => $reservations
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener las reservas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una nueva reserva
     */
    public function store(Request $request)
    {
        try {
            $reservation = new Reservation();
            if ($request->hotel_id && $request->room_id && $request->room_type_id && 
                $request->check_in_date && $request->check_out_date && $request->number_of_guests && 
                $request->guest_name && $request->guest_email && $request->total_price) {
                
                // Verificar que el hotel existe
                $hotel = hotelModel::find($request->hotel_id);
                if (!$hotel) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Hotel no encontrado'
                    ], 404);
                }

                // Verificar que la habitación existe
                $room = Room::find($request->room_id);
                if (!$room) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Habitación no encontrada'
                    ], 404);
                }

                // Verificar que el tipo de habitación existe
                $roomType = RoomType::find($request->room_type_id);
                if (!$roomType) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Tipo de habitación no encontrado'
                    ], 404);
                }

                // Validar fechas
                $checkIn = Carbon::parse($request->check_in_date);
                $checkOut = Carbon::parse($request->check_out_date);
                
                if ($checkIn >= $checkOut) {
                    return response()->json([
                        'success' => false,
                        'error' => 'La fecha de check-in debe ser anterior a la fecha de check-out'
                    ], 400);
                }

                if ($checkIn < Carbon::now()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'La fecha de check-in debe ser futura'
                    ], 400);
                }

                // Verificar disponibilidad de la habitación
                $overlapping = Reservation::where('room_id', $request->room_id)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($query) use ($checkIn, $checkOut) {
                        $query->where(function ($q) use ($checkIn, $checkOut) {
                            $q->where('check_in_date', '<=', $checkIn)
                                ->where('check_out_date', '>', $checkIn);
                        })->orWhere(function ($q) use ($checkIn, $checkOut) {
                            $q->where('check_in_date', '<', $checkOut)
                                ->where('check_out_date', '>=', $checkOut);
                        })->orWhere(function ($q) use ($checkIn, $checkOut) {
                            $q->where('check_in_date', '>=', $checkIn)
                                ->where('check_out_date', '<=', $checkOut);
                        });
                    })
                    ->first();

                if ($overlapping) {
                    return response()->json([
                        'success' => false,
                        'error' => 'La habitación no está disponible para las fechas seleccionadas'
                    ], 400);
                }

                // Validar capacidad
                if ($request->number_of_guests > $roomType->max_capacity) {
                    return response()->json([
                        'success' => false,
                        'error' => 'El número de huéspedes excede la capacidad máxima de la habitación'
                    ], 400);
                }

                // Crear la reserva
                $reservation->hotel_id = $request->hotel_id;
                $reservation->room_id = $request->room_id;
                $reservation->room_type_id = $request->room_type_id;
                $reservation->check_in_date = $request->check_in_date;
                $reservation->check_out_date = $request->check_out_date;
                $reservation->number_of_guests = $request->number_of_guests;
                $reservation->guest_name = $request->guest_name;
                $reservation->guest_email = $request->guest_email;
                $reservation->guest_phone = $request->guest_phone;
                $reservation->total_price = $request->total_price;
                $reservation->status = $request->status ?? 'confirmed';
                $reservation->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Reserva creada exitosamente',
                    'data' => $reservation->load(['hotel', 'room.roomType', 'roomType'])
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Los campos hotel_id, room_id, room_type_id, check_in_date, check_out_date, number_of_guests, guest_name, guest_email y total_price son obligatorios'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear la reserva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una reserva específica
     */
    public function show($id)
    {
        try {
            $reservation = Reservation::with(['hotel', 'room.roomType', 'roomType'])->find($id);
            
            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'error' => 'Reserva no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $reservation
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener la reserva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar una reserva existente
     */
    public function update(Request $request, $id)
    {
        try {
            $reservation = Reservation::find($id);
            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'error' => 'Reserva no encontrada'
                ], 404);
            }

            // Solo permitir actualizar ciertos campos después de crear la reserva
            if ($request->guest_name) {
                $reservation->guest_name = $request->guest_name;
            }
            if ($request->guest_email) {
                $reservation->guest_email = $request->guest_email;
            }
            if ($request->guest_phone) {
                $reservation->guest_phone = $request->guest_phone;
            }
            if ($request->status && in_array($request->status, ['confirmed', 'cancelled', 'completed'])) {
                $reservation->status = $request->status;
            }

            $reservation->save();

            return response()->json([
                'success' => true,
                'message' => 'Reserva actualizada exitosamente',
                'data' => $reservation->load(['hotel', 'room.roomType', 'roomType'])
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar la reserva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar/Cancelar una reserva
     */
    public function destroy($id)
    {
        try {
            $reservation = Reservation::find($id);
            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'error' => 'Reserva no encontrada'
                ], 404);
            }

            // En lugar de eliminar, marcar como cancelada
            $reservation->status = 'cancelled';
            $reservation->save();

            return response()->json([
                'success' => true,
                'message' => 'Reserva cancelada exitosamente'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cancelar la reserva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener reservas por hotel
     */
    public function getByHotel($hotel_id)
    {
        try {
            $hotel = hotelModel::find($hotel_id);
            if (!$hotel) {
                return response()->json([
                    'success' => false,
                    'error' => 'Hotel no encontrado'
                ], 404);
            }

            $reservations = Reservation::where('hotel_id', $hotel_id)
                ->with(['room.roomType', 'roomType'])
                ->orderBy('check_in_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'hotel' => $hotel,
                    'reservations' => $reservations
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener las reservas del hotel: ' . $e->getMessage()
            ], 500);
        }
    }
}
