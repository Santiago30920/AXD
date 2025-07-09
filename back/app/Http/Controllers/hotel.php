<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\hotelModel;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\temporateModel;
use Carbon\Carbon;
use Exception;

class hotel extends Controller
{
    /**
     * Obtener todos los hoteles disponibles
     */
    public function index()
    {
        $hotels = hotelModel::with(['rooms.roomType', 'temporate'])->get();

        return response()->json([
            'success' => true,
            'data' => $hotels
        ]);
    }

    /**
     * Verificar disponibilidad de hoteles según fechas
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1'
        ]);

        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        $numberOfGuests = $request->number_of_guests;

        $hotels = hotelModel::with(['rooms.roomType', 'reservations'])->get();
        $availableHotels = [];

        foreach ($hotels as $hotel) {
            // Obtener habitaciones ocupadas en esas fechas
            $occupiedRooms = Reservation::where('hotel_id', $hotel->id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                        ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                        ->orWhere(function ($q) use ($checkIn, $checkOut) {
                            $q->where('check_in_date', '<=', $checkIn)
                                ->where('check_out_date', '>=', $checkOut);
                        });
                })
                ->pluck('room_id')
                ->toArray();

            // Obtener habitaciones disponibles
            $availableRooms = $hotel->rooms()
                ->whereNotIn('id', $occupiedRooms)
                ->where('is_available', true)
                ->with('roomType')
                ->get();

            // Verificar si hay habitaciones que puedan acomodar a los huéspedes
            $suitableRooms = $availableRooms->filter(function ($room) use ($numberOfGuests) {
                return $room->roomType->max_capacity >= $numberOfGuests;
            });

            if ($suitableRooms->count() > 0) {
                $availableHotels[] = [
                    'hotel' => $hotel,
                    'available_rooms' => $suitableRooms->values(),
                    'total_available_rooms' => $suitableRooms->count()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $availableHotels,
            'search_criteria' => [
                'check_in_date' => $checkIn->format('Y-m-d'),
                'check_out_date' => $checkOut->format('Y-m-d'),
                'number_of_guests' => $numberOfGuests
            ]
        ]);
    }

    /**
     * Obtener tarifas según hotel, temporada, personas y tipo de alojamiento
     */
    public function getRates(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'room_type_id' => 'nullable|exists:room_types,id'
        ]);

        $hotel = hotelModel::with(['temporate'])->find($request->hotel_id);
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        $numberOfGuests = $request->number_of_guests;
        $nights = $checkIn->diffInDays($checkOut);

        // Si se especifica un tipo de habitación, solo mostrar ese
        if ($request->room_type_id) {
            $roomTypes = RoomType::where('id', $request->room_type_id)->get();
        } else {
            $roomTypes = RoomType::all();
        }

        $rates = [];

        foreach ($roomTypes as $roomType) {
            // Verificar si este tipo de habitación puede acomodar a los huéspedes
            if ($roomType->max_capacity >= $numberOfGuests) {
                // Obtener multiplicador de temporada
                $seasonMultiplier = $this->getSeasonMultiplier($hotel, $checkIn, $checkOut);

                // Calcular precios
                $basePrice = $roomType->base_price;
                $subtotal = $basePrice * $nights;
                $totalPrice = $subtotal * $seasonMultiplier;

                $rates[] = [
                    'room_type_id' => $roomType->id,
                    'room_type_name' => $roomType->name,
                    'max_capacity' => $roomType->max_capacity,
                    'base_price_per_night' => $basePrice,
                    'nights' => $nights,
                    'season_multiplier' => $seasonMultiplier,
                    'subtotal' => round($subtotal, 2),
                    'total_price' => round($totalPrice, 2)
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'hotel' => $hotel,
                'rates' => $rates,
                'search_criteria' => [
                    'check_in_date' => $checkIn->format('Y-m-d'),
                    'check_out_date' => $checkOut->format('Y-m-d'),
                    'number_of_guests' => $numberOfGuests,
                    'nights' => $nights
                ]
            ]
        ]);
    }

    /**
     * Calcular tarifa total específica
     */
    public function calculateRate(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1'
        ]);

        $hotel = hotelModel::with(['temporate'])->find($request->hotel_id);
        $roomType = RoomType::find($request->room_type_id);
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        $numberOfGuests = $request->number_of_guests;
        $nights = $checkIn->diffInDays($checkOut);

        // Verificar capacidad
        if ($numberOfGuests > $roomType->max_capacity) {
            return response()->json([
                'success' => false,
                'error' => 'El número de huéspedes excede la capacidad máxima de la habitación'
            ], 400);
        }

        // Verificar disponibilidad
        $occupiedRooms = Reservation::where('hotel_id', $hotel->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                    });
            })
            ->join('rooms', 'reservations.room_id', '=', 'rooms.id')
            ->where('rooms.room_type_id', $roomType->id)
            ->pluck('rooms.id')
            ->toArray();

        $availableRooms = Room::where('hotel_id', $hotel->id)
            ->where('room_type_id', $roomType->id)
            ->where('is_available', true)
            ->whereNotIn('id', $occupiedRooms)
            ->get();

        if ($availableRooms->count() == 0) {
            return response()->json([
                'success' => false,
                'error' => 'No hay habitaciones disponibles de este tipo para las fechas seleccionadas'
            ], 400);
        }

        // Calcular precios
        $seasonMultiplier = $this->getSeasonMultiplier($hotel, $checkIn, $checkOut);
        $basePrice = $roomType->base_price;
        $subtotal = $basePrice * $nights;
        $totalPrice = $subtotal * $seasonMultiplier;

        return response()->json([
            'success' => true,
            'data' => [
                'hotel' => $hotel->name,
                'room_type' => $roomType->name,
                'calculation' => [
                    'check_in_date' => $checkIn->format('Y-m-d'),
                    'check_out_date' => $checkOut->format('Y-m-d'),
                    'nights' => $nights,
                    'number_of_guests' => $numberOfGuests,
                    'base_price_per_night' => $basePrice,
                    'season_multiplier' => $seasonMultiplier,
                    'subtotal' => round($subtotal, 2),
                    'total_price' => round($totalPrice, 2)
                ],
                'available_rooms' => $availableRooms->count()
            ]
        ]);
    }

    /**
     * Realizar una reserva
     */
    public function makeReservation(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email',
            'guest_phone' => 'nullable|string'
        ]);

        // Primero calcular la tarifa para verificar disponibilidad
        $calculation = $this->calculateRate($request);
        $calculationData = $calculation->getData();

        if (!$calculationData->success) {
            return $calculation;
        }

        // Obtener una habitación disponible
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);

        $occupiedRooms = Reservation::where('hotel_id', $request->hotel_id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                    });
            })
            ->join('rooms', 'reservations.room_id', '=', 'rooms.id')
            ->where('rooms.room_type_id', $request->room_type_id)
            ->pluck('rooms.id')
            ->toArray();

        $availableRoom = Room::where('hotel_id', $request->hotel_id)
            ->where('room_type_id', $request->room_type_id)
            ->where('is_available', true)
            ->whereNotIn('id', $occupiedRooms)
            ->first();

        // Crear la reserva
        $reservation = Reservation::create([
            'hotel_id' => $request->hotel_id,
            'room_id' => $availableRoom->id,
            'room_type_id' => $request->room_type_id,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'number_of_guests' => $request->number_of_guests,
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'total_price' => $calculationData->data->calculation->total_price,
            'status' => 'confirmed'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reserva creada exitosamente',
            'data' => [
                'reservation' => $reservation->load(['hotel', 'room', 'roomType']),
                'calculation' => $calculationData->data->calculation
            ]
        ], 201);
    }

    /**
     * Obtener todas las reservas
     */
    public function getReservations()
    {
        $reservations = Reservation::with(['hotel', 'room.roomType', 'roomType'])->get();

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Obtener tipos de habitación disponibles
     */
    public function getRoomTypes()
    {
        $roomTypes = RoomType::all();

        return response()->json([
            'success' => true,
            'data' => $roomTypes
        ]);
    }

    /**
     * Crear un nuevo hotel
     */
    public function store(Request $request)
    {
        try {
            $hotel = new hotelModel();
            if ($request->name && $request->address && $request->city && $request->state) {
                // Validar que los campos requeridos estén presentes
                $hotel->name = $request->name;
                $hotel->address = $request->address;
                $hotel->city = $request->city;
                $hotel->state = $request->state;
                $hotel->capacity = $request->capacity ?? 1; // Asignar capacidad mínima de 1 si no se proporciona
                $hotel->phone = $request->phone;
                $hotel->email = $request->email;
                $hotel->description = $request->description;
                $hotel->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Hotel creado exitosamente',
                    'data' => $hotel
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Los campos name, address, city y state son obligatorios'
                ], 400);

            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear el hotel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un hotel específico
     */
    public function show($id)
    {
        $hotel = hotelModel::with(['rooms.roomType', 'temporate', 'reservations'])
            ->find($id);

        if (!$hotel) {
            return response()->json([
                'success' => false,
                'error' => 'Hotel no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $hotel
        ]);
    }

    /**
     * Actualizar un hotel existente
     */
    public function update(Request $request, $id)
    {
        try {
            if ($request->name && $request->address && $request->city && $request->state) {
                $response = hotelModel::weher('id', $id)->update([
                    'name' => $request->name,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'capacity' => $request->capacity ?? 1, // Asignar capacidad mínima de 1 si no se proporciona
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'description' => $request->description
                ]);
                if ($response == 1) {
                    return response()->json([
                        "status" => 200,
                        "message" => "Hotel updated successfully",
                        "code" => 1
                    ], 200);
                } else {
                    return response()->json([
                        "status" => 500,
                        "message" => "Hotel not update, id invalid",
                        "code" => 0
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Los campos name, address, city y state son obligatorios'
                ], 400);

            }
        }
        catch(Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar el hotel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un hotel
     */
    public function destroy($id)
    {
        try {
            $response = hotelModel::where('id', '=', $id)->delete();
            if ($response == 1) {
                return response()->json([
                    "status" => 200,
                    "message" => "Hotal deleted successfully",
                    "code" => 1
                ], 200);
            } else {
                return response()->json([
                    "status" => 500,
                    "message" => "Hotel not deleted",
                    "code" => 0
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Hotel not deleted",
                "messageLog" => $e->getMessage(),
                "code" => 0
            ], 500);
        }
    }

    /**
     * Método privado para obtener el multiplicador de temporada
     */
    private function getSeasonMultiplier($hotel, $checkIn, $checkOut)
    {
        $multiplier = 1.0; // Por defecto temporada baja

        // Buscar temporadas que se solapen con las fechas de la reserva
        $seasons = $hotel->temporate()
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    // La temporada empieza antes y termina después del check-in
                    $q->where('start_date', '<=', $checkIn)
                        ->where('end_date', '>=', $checkIn);
                })->orWhere(function ($q) use ($checkIn, $checkOut) {
                    // La temporada empieza antes y termina después del check-out
                    $q->where('start_date', '<=', $checkOut)
                        ->where('end_date', '>=', $checkOut);
                })->orWhere(function ($q) use ($checkIn, $checkOut) {
                    // La temporada está completamente dentro de las fechas de reserva
                    $q->where('start_date', '>=', $checkIn)
                        ->where('end_date', '<=', $checkOut);
                });
            })
            ->get();

        // Usar el multiplicador más alto encontrado
        foreach ($seasons as $season) {
            if ($season->price_multiplier > $multiplier) {
                $multiplier = $season->price_multiplier;
            }
        }

        return $multiplier;
    }
}
