<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AchatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $boutiques = Achat::with(['produit', 'creator', 'updater', 'deleter'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des achats récupérée avec succès.',
                'data' => $boutiques,
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
                'quantity' => 'required|integer|min:1',
                'amount' => 'required|integer|min:0',
                'message' => 'nullable|string|max:255',
                'date' => 'required',
                'produit_id' => 'required|exists:produits,id',
            ]);

            $achat = Achat::create(array_merge($validated, [
                'created_by' => Auth::id(),
            ]));

            $produit = Produit::findById($validated['produit_id']);

            $countProduit = $produit->quantity + $validated['quantity'];
            $produit->update([
                'quantity' => $countProduit,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Achat effectuer avec succès.',
                'data' => $achat
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
            $achat = Achat::findById($id);
            $achat->load(['produit', 'creator', 'updater', 'deleter']);

            return response()->json([
                'success' => true,
                'message' => 'Achat récupérée avec succès.',
                'data' => $achat
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Achat non trouvée.',
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
            $achat = Achat::findById($id);

            $validated = $request->validate([
                'quantity' => 'required|string|max:255',
                'amount' => 'required|int',
                'message' => 'nullable|string|max:255',
                'date' => 'required',
                'produit_id' => 'required|exists:produits,id',
            ]);

            $achat->update(array_merge($validated, [
                'updated_by' => Auth::id(),
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Achat mise à jour avec succès.',
                'data' => $achat
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
                'message' => 'Achat non trouvée.',
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
            $achat = Achat::findById($id);

            $achat->update(['deleted_by' => Auth::id()]);
            $achat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Achat supprimée avec succès.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Achat non trouvée.',
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
