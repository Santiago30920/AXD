<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomType;
use Exception;

class RoomTypeController extends Controller
{
    /**
     * Obtener todos los tipos de habitación
     */
    public function index()
    {
        try {
            $roomTypes = RoomType::with(['rooms'])->get();
            return response()->json([
                'success' => true,
                'data' => $roomTypes
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener los tipos de habitación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo tipo de habitación
     */
    public function store(Request $request)
    {
        try {
            $roomType = new RoomType();
            if ($request->name && $request->base_price && $request->max_capacity) {
                // Validar que los campos requeridos estén presentes
                $roomType->name = $request->name;
                $roomType->base_price = $request->base_price;
                $roomType->max_capacity = $request->max_capacity;
                $roomType->description = $request->description;
                $roomType->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Tipo de habitación creado exitosamente',
                    'data' => $roomType
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Los campos name, base_price y max_capacity son obligatorios'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear el tipo de habitación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un tipo de habitación específico
     */
    public function show($id)
    {
        try {
            $roomType = RoomType::with(['rooms.hotel'])->find($id);
            
            if (!$roomType) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tipo de habitación no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $roomType
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener el tipo de habitación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un tipo de habitación existente
     */
    public function update(Request $request, $id)
    {
        try {
            $roomType = RoomType::find($id);
            if (!$roomType) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tipo de habitación no encontrado'
                ], 404);
            }

            if ($request->name && $request->base_price && $request->max_capacity) {
                // Validar que los campos requeridos estén presentes
                $roomType->name = $request->name;
                $roomType->base_price = $request->base_price;
                $roomType->max_capacity = $request->max_capacity;
                $roomType->description = $request->description;
                $roomType->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Tipo de habitación actualizado exitosamente',
                    'data' => $roomType
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Los campos name, base_price y max_capacity son obligatorios'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar el tipo de habitación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un tipo de habitación
     */
    public function destroy($id)
    {
        try {
            $roomType = RoomType::find($id);
            if (!$roomType) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tipo de habitación no encontrado'
                ], 404);
            }

            // Verificar si tiene habitaciones asociadas
            if ($roomType->rooms()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'No se puede eliminar el tipo de habitación porque tiene habitaciones asociadas'
                ], 400);
            }

            $roomType->delete();
            return response()->json([
                'success' => true,
                'message' => 'Tipo de habitación eliminado exitosamente'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar el tipo de habitación: ' . $e->getMessage()
            ], 500);
        }
    }
}
