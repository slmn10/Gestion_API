<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VenteController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $ventes = Vente::with(['produits', 'creator', 'updater', 'deleter'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des ventes récupérée avec succès.',
                'data' => $ventes,
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
                'amount' => 'required|integer',
                'message' => 'nullable|string|max:255',
                'produit' => 'required|exists:produits,id',
                'date' => 'required',
            ]);

            $vente = Vente::create(array_merge($validated, [
                'created_by' => Auth::id(),
            ]));

            $produit = Produit::findById($validated['produit_id']);
            $countProduit = $produit->quantity - $validated['quantity'];
            $produit->updater([
                'quantity' => $countProduit
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vente effectuer avec succès.',
                'data' => $vente
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
            $vente = Vente::findById($id);
            $vente->load(['produits', 'creator', 'updater', 'deleter']);

            return response()->json([
                'success' => true,
                'message' => 'Vente récupérée avec succès.',
                'data' => $vente
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vente non trouvée.',
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
            $vente = Vente::findById($id);

            $validated = $request->validate([
                'quantity' => 'required|string|max:255',
                'amount' => 'required|int',
                'message' => 'nullable|string|max:255',
                'date' => 'required',
            ]);

            $vente->update(array_merge($validated, [
                'updated_by' => Auth::id(),
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Vente mise à jour avec succès.',
                'data' => $vente
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
                'message' => 'Vente non trouvée.',
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
            $vente = Vente::findById($id);

            $vente->update(['deleted_by' => Auth::id()]);
            $vente->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vente supprimée avec succès.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vente non trouvée.',
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
