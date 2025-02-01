<?php

namespace App\Http\Controllers;

use App\Models\Perte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerteController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $pertes = Perte::with(['creator', 'updater', 'deleter'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des pertes récupérée avec succès.',
                'data' => $pertes,
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
                'message' => 'required|string|max:255',
                'amount' => 'nullable|integer|min:0',
                'date' => 'required',
            ]);

            $perte = Perte::create(array_merge($validated, [
                'created_by' => Auth::id(),
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Perte effectuer avec succès.',
                'data' => $perte
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
            $perte = Perte::findById($id);
            $perte->load(['creator', 'updater', 'deleter']);

            return response()->json([
                'success' => true,
                'message' => 'Perte récupérée avec succès.',
                'data' => $perte
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perte non trouvée.',
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
            $perte = Perte::findById($id);

            $validated = $request->validate([
                'message' => 'required|string|max:255',
                'amount' => 'nullable|integer|min:0',
                'date' => 'required',
            ]);

            $perte->update(array_merge($validated, [
                'updated_by' => Auth::id(),
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Perte mise à jour avec succès.',
                'data' => $perte
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
                'message' => 'Perte non trouvée.',
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
            $perte = Perte::findById($id);

            $perte->update(['deleted_by' => Auth::id()]);
            $perte->delete();

            return response()->json([
                'success' => true,
                'message' => 'Perte supprimée avec succès.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perte non trouvée.',
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
