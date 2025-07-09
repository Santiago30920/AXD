<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\temporateModel; // Assuming you have a Type model

class temporate extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $types = temporateModel::all();
            return response()->json([
                "status" => 200,
                "data" => $types
            ], 200);

        } catch (Exception $e) {
            // Handle the exception
            return response()->json(['error' => 'An error occurred while fetching the temporate records.'], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            $temporate = new temporateModel();
            $temporate->temporate = $request->temporate;
            $temporate->price = $request->price;
            $temporate->save();

            return response()->json([
                "status" => 201,
                "message" => "Temporate created successfully",
                "data" => $temporate
            ], 201);

        } catch (Exception $e) {
            // Handle the exception
            return response()->json(['error' => 'An error occurred while creating the temporate record.'], 500);
        }
    }
    public function show($id)
    {
        try {
            $temporate = temporateModel::findOrFail($id);
            return response()->json([
                "status" => 200,
                "data" => $temporate
            ], 200);

        } catch (Exception $e) {
            // Handle the exception
            return response()->json(['error' => 'Temporate record not found.'], 404);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $temporate = temporateModel::findOrFail($id);
            $temporate->temporate = $request->temporate;
            $temporate->price = $request->price;
            $temporate->save();
            return response()->json([
                "status" => 200,
                "message" => "Temporate updated successfully",
                "data" => $temporate
            ], 200);
        } catch (Exception $e) {
            // Handle the exception
            return response()->json(['error' => 'An error occurred while updating the temporate record.'], 500);
        }
    }
}
