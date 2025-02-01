<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $produits = Produit::with(['creator', 'updater', 'deleter'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des produits récupérée avec succès.',
                'data' => $produits,
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
                'imageUri' => 'nullable|file',
                'name' => 'required|string|max:255',
            ]);

            if ($request->hasFile('imageUri')) {
                $image = $request->file('imageUri');

                if ($image instanceof \Illuminate\Http\UploadedFile) {
                    // Stocker l'image dans "storage/app/public/images"
                    $path = $image->store('images', 'public');
                    // Enregistrer le chemin absolu dans imageUrl
                    $validated['imageUrl'] = asset(Storage::url($path));
                }
            }

            $produit = Produit::create(array_merge($validated, [
                'created_by' => Auth::id(),
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté avec succès.',
                'data' => $produit
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
            $produit = Produit::findById($id);
            $produit->load(['achats', 'ventes', 'creator', 'updater', 'deleter']);

            return response()->json([
                'success' => true,
                'message' => 'Produit récupérée avec succès.',
                'data' => $produit
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'produit non trouvée.',
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
            // Recherche du produit ou renvoi une erreur 404
            $produit = Produit::findOrFail($id);

            $validated = $request->validate([
                'imageUri' => 'nullable|file',
                'name' => 'required|string|max:255',
            ]);

            if ($request->hasFile('imageUri')) {
                // Suppression de l'ancienne image si elle existe
                if ($produit->imageUrl) {
                    $oldPath = str_replace('/storage', 'public', $produit->imageUrl);
                    if (Storage::exists($oldPath)) {
                        Storage::delete($oldPath);
                    }
                }

                // Stockage de la nouvelle image
                $path = $request->file('imageUri')->store('images', 'public');
                // Enregistrer le chemin absolu dans imageUrl
                $validated['imageUrl'] = asset(Storage::url($path));
            }

            // Mise à jour du produit
            $produit->update(array_merge($validated, [
                'updated_by' => Auth::id(),
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Produit mis à jour avec succès.',
                'data' => $produit
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
                'message' => 'Produit non trouvé.',
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
            // Trouver le produit ou renvoyer une erreur 404
            $produit = Produit::findOrFail($id);

            // Supprimer l'image associée si elle existe
            if ($produit->imageUrl) {
                $oldPath = str_replace('/storage', 'public', $produit->imageUrl);
                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
            }

            // Enregistrer l'utilisateur qui supprime l'élément
            $produit->update(['deleted_by' => Auth::id()]);

            // Supprimer le produit
            $produit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produit supprimé avec succès.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé.',
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
