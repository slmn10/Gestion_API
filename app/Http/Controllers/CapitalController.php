<?php

namespace App\Http\Controllers;

use App\Models\Capital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CapitalController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $capitals = Capital::with(['creator', 'updater', 'deleter'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des capitals récupérée avec succès.',
                'data' => $capitals,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Veuillez réessayer plus tard.',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'source' => 'required|string|max:255',
                'amount' => 'required|integer|min:0',
                'message' => 'nullable|string|max:255',
                'date' => 'required',
            ]);

            $capital = Capital::create(array_merge($validated, [
                'created_by' => Auth::id(),
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Capital effectuer avec succès.',
                'data' => $capital
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation des données.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Veuillez réessayer plus tard.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $capital = Capital::findById($id);
            $capital->load(['creator', 'updater', 'deleter']);

            return response()->json([
                'success' => true,
                'message' => 'Capital récupérée avec succès.',
                'data' => $capital
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Capital non trouvée.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Veuillez réessayer plus tard.',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $capital = Capital::findById($id);

            $validated = $request->validate([
                'source' => 'required|string|max:255',
                'amount' => 'required|integer|min:0',
                'message' => 'nullable|string|max:255',
                'date' => 'required',
            ]);

            $capital->update(array_merge($validated, [
                'updated_by' => Auth::id(),
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Capital mise à jour avec succès.',
                'data' => $capital
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation des données.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Capital non trouvée.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Veuillez réessayer plus tard.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $capital = Capital::findById($id);

            $capital->update(['deleted_by' => Auth::id()]);
            $capital->delete();

            return response()->json([
                'success' => true,
                'message' => 'Capital supprimée avec succès.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Capital non trouvée.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Veuillez réessayer plus tard.',
            ], 500);
        }
    }
}
