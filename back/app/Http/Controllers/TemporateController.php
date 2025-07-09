<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\temporateModel;
use App\Models\hotelModel;
use Carbon\Carbon;
use Exception;

class TemporateController extends Controller
{
    /**
     * Obtener todas las temporadas
     */
    public function index()
    {
        try {
            $temporates = temporateModel::with(['hotel'])->get();
            return response()->json([
                'success' => true,
                'data' => $temporates
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener las temporadas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una nueva temporada
     */
    public function store(Request $request)
    {
        try {
            $temporate = new temporateModel();
            if ($request->hotel_id && $request->season && $request->start_date && $request->end_date && $request->price_multiplier) {
                // Verificar que el hotel existe
                $hotel = hotelModel::find($request->hotel_id);
                if (!$hotel) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Hotel no encontrado'
                    ], 404);
                }

                // Validar que la fecha de inicio sea anterior a la fecha de fin
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
                
                if ($startDate >= $endDate) {
                    return response()->json([
                        'success' => false,
                        'error' => 'La fecha de inicio debe ser anterior a la fecha de fin'
                    ], 400);
                }

                // Validar que el season sea válido
                if (!in_array($request->season, ['alta', 'baja'])) {
                    return response()->json([
                        'success' => false,
                        'error' => 'La temporada debe ser "alta" o "baja"'
                    ], 400);
                }

                // Validar que el multiplicador de precio sea válido
                if ($request->price_multiplier <= 0) {
                    return response()->json([
                        'success' => false,
                        'error' => 'El multiplicador de precio debe ser mayor a 0'
                    ], 400);
                }

                // Verificar solapamiento de fechas para el mismo hotel
                $overlapping = temporateModel::where('hotel_id', $request->hotel_id)
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->where(function ($q) use ($startDate, $endDate) {
                            // La nueva temporada empieza durante una existente
                            $q->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $startDate);
                        })->orWhere(function ($q) use ($startDate, $endDate) {
                            // La nueva temporada termina durante una existente
                            $q->where('start_date', '<=', $endDate)
                                ->where('end_date', '>=', $endDate);
                        })->orWhere(function ($q) use ($startDate, $endDate) {
                            // Una temporada existente está completamente dentro de la nueva
                            $q->where('start_date', '>=', $startDate)
                                ->where('end_date', '<=', $endDate);
                        });
                    })
                    ->first();

                if ($overlapping) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Ya existe una temporada que se solapa con las fechas proporcionadas'
                    ], 400);
                }

                // Validar que los campos requeridos estén presentes
                $temporate->hotel_id = $request->hotel_id;
                $temporate->season = $request->season;
                $temporate->start_date = $request->start_date;
                $temporate->end_date = $request->end_date;
                $temporate->price_multiplier = $request->price_multiplier;
                $temporate->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Temporada creada exitosamente',
                    'data' => $temporate->load(['hotel'])
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Los campos hotel_id, season, start_date, end_date y price_multiplier son obligatorios'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear la temporada: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una temporada específica
     */
    public function show($id)
    {
        try {
            $temporate = temporateModel::with(['hotel'])->find($id);
            
            if (!$temporate) {
                return response()->json([
                    'success' => false,
                    'error' => 'Temporada no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $temporate
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener la temporada: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar una temporada existente
     */
    public function update(Request $request, $id)
    {
        try {
            $temporate = temporateModel::find($id);
            if (!$temporate) {
                return response()->json([
                    'success' => false,
                    'error' => 'Temporada no encontrada'
                ], 404);
            }

            if ($request->hotel_id && $request->season && $request->start_date && $request->end_date && $request->price_multiplier) {
                // Verificar que el hotel existe
                $hotel = hotelModel::find($request->hotel_id);
                if (!$hotel) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Hotel no encontrado'
                    ], 404);
                }

                // Validar que la fecha de inicio sea anterior a la fecha de fin
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
                
                if ($startDate >= $endDate) {
                    return response()->json([
                        'success' => false,
                        'error' => 'La fecha de inicio debe ser anterior a la fecha de fin'
                    ], 400);
                }

                // Validar que el season sea válido
                if (!in_array($request->season, ['alta', 'baja'])) {
                    return response()->json([
                        'success' => false,
                        'error' => 'La temporada debe ser "alta" o "baja"'
                    ], 400);
                }

                // Validar que el multiplicador de precio sea válido
                if ($request->price_multiplier <= 0) {
                    return response()->json([
                        'success' => false,
                        'error' => 'El multiplicador de precio debe ser mayor a 0'
                    ], 400);
                }

                // Verificar solapamiento de fechas para el mismo hotel (excluyendo la temporada actual)
                $overlapping = temporateModel::where('hotel_id', $request->hotel_id)
                    ->where('id', '!=', $id)
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->where(function ($q) use ($startDate, $endDate) {
                            // La temporada actualizada empieza durante una existente
                            $q->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $startDate);
                        })->orWhere(function ($q) use ($startDate, $endDate) {
                            // La temporada actualizada termina durante una existente
                            $q->where('start_date', '<=', $endDate)
                                ->where('end_date', '>=', $endDate);
                        })->orWhere(function ($q) use ($startDate, $endDate) {
                            // Una temporada existente está completamente dentro de la actualizada
                            $q->where('start_date', '>=', $startDate)
                                ->where('end_date', '<=', $endDate);
                        });
                    })
                    ->first();

                if ($overlapping) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Ya existe una temporada que se solapa con las fechas proporcionadas'
                    ], 400);
                }

                // Validar que los campos requeridos estén presentes
                $temporate->hotel_id = $request->hotel_id;
                $temporate->season = $request->season;
                $temporate->start_date = $request->start_date;
                $temporate->end_date = $request->end_date;
                $temporate->price_multiplier = $request->price_multiplier;
                $temporate->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Temporada actualizada exitosamente',
                    'data' => $temporate->load(['hotel'])
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Los campos hotel_id, season, start_date, end_date y price_multiplier son obligatorios'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar la temporada: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una temporada
     */
    public function destroy($id)
    {
        try {
            $temporate = temporateModel::find($id);
            if (!$temporate) {
                return response()->json([
                    'success' => false,
                    'error' => 'Temporada no encontrada'
                ], 404);
            }

            $temporate->delete();
            return response()->json([
                'success' => true,
                'message' => 'Temporada eliminada exitosamente'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar la temporada: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener temporadas por hotel
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

            $temporates = temporateModel::where('hotel_id', $hotel_id)
                ->orderBy('start_date', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'hotel' => $hotel,
                    'temporates' => $temporates
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener las temporadas del hotel: ' . $e->getMessage()
            ], 500);
        }
    }
}
