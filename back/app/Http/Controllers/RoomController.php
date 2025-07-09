<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\hotelModel;
use App\Models\RoomType;
use Exception;

class RoomController extends Controller
{
    /**
     * Obtener todas las habitaciones
     */
    public function index()
    {
        try {
            $rooms = Room::with(['hotel', 'roomType'])->get();
            return response()->json([
                'success' => true,
                'data' => $rooms
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener las habitaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una nueva habitación
     */
    public function store(Request $request)
    {
        try {
            $room = new Room();
            if ($request->hotel_id && $request->room_type_id && $request->room_number) {
                // Verificar que el hotel existe
                $hotel = hotelModel::find($request->hotel_id);
                if (!$hotel) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Hotel no encontrado'
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

                // Verificar que el número de habitación no esté duplicado en el mismo hotel
                $existingRoom = Room::where('hotel_id', $request->hotel_id)
                    ->where('room_number', $request->room_number)
                    ->first();
                
                if ($existingRoom) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Ya existe una habitación con este número en el hotel'
                    ], 400);
                }

                // Validar que los campos requeridos estén presentes
                $room->hotel_id = $request->hotel_id;
                $room->room_type_id = $request->room_type_id;
                $room->room_number = $request->room_number;
                $room->is_available = $request->is_available ?? true; // Por defecto disponible
                $room->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Habitación creada exitosamente',
                    'data' => $room->load(['hotel', 'roomType'])
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Los campos hotel_id, room_type_id y room_number son obligatorios'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear la habitación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una habitación específica
     */
    public function show($id)
    {
        try {
            $room = Room::with(['hotel', 'roomType', 'reservations'])->find($id);
            
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'error' => 'Habitación no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $room
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener la habitación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar una habitación existente
     */
    public function update(Request $request, $id)
    {
        try {
            $room = Room::find($id);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'error' => 'Habitación no encontrada'
                ], 404);
            }

            if ($request->hotel_id && $request->room_type_id && $request->room_number) {
                // Verificar que el hotel existe
                $hotel = hotelModel::find($request->hotel_id);
                if (!$hotel) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Hotel no encontrado'
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

                // Verificar que el número de habitación no esté duplicado en el mismo hotel (excluyendo la habitación actual)
                $existingRoom = Room::where('hotel_id', $request->hotel_id)
                    ->where('room_number', $request->room_number)
                    ->where('id', '!=', $id)
                    ->first();
                
                if ($existingRoom) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Ya existe una habitación con este número en el hotel'
                    ], 400);
                }

                // Validar que los campos requeridos estén presentes
                $room->hotel_id = $request->hotel_id;
                $room->room_type_id = $request->room_type_id;
                $room->room_number = $request->room_number;
                $room->is_available = $request->is_available ?? $room->is_available;
                $room->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Habitación actualizada exitosamente',
                    'data' => $room->load(['hotel', 'roomType'])
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Los campos hotel_id, room_type_id y room_number son obligatorios'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar la habitación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una habitación
     */
    public function destroy($id)
    {
        try {
            $room = Room::find($id);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'error' => 'Habitación no encontrada'
                ], 404);
            }

            // Verificar si tiene reservas asociadas
            if ($room->reservations()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'No se puede eliminar la habitación porque tiene reservas asociadas'
                ], 400);
            }

            $room->delete();
            return response()->json([
                'success' => true,
                'message' => 'Habitación eliminada exitosamente'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar la habitación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener habitaciones por hotel
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

            $rooms = Room::where('hotel_id', $hotel_id)
                ->with(['roomType'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'hotel' => $hotel,
                    'rooms' => $rooms
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener las habitaciones del hotel: ' . $e->getMessage()
            ], 500);
        }
    }
}
