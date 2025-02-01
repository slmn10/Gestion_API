<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                
                // Générer un nom de fichier unique
                $filename = time() . '.' . $request->file('imageUri')->getClientOriginalExtension();

                // Définir le chemin de stockage dans le dossier public
                $path = public_path('storage/images');

                // Vérifier si le dossier existe, sinon le créer
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                // Déplacer l'image dans le dossier public
                $request->file('imageUri')->move($path, $filename);

                // Ajouter le chemin de la nouvelle photo dans les données à mettre à jour
                $validated['imageUrl'] = 'storage/images/' . $filename;
            }

            $produit = Produit::create(array_merge($validated, [
                'created_by' => Auth::id(),
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Produit ajouter avec succès.',
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
            $produit = Produit::findById($id);

            $validated = $request->validate([
                'imageUri' => 'nullable|file',
                'name' => 'required|string|max:255',
            ]);

            if ($request->hasFile('imageUri')) {

                if ($produit->imageUrl && file_exists(public_path($produit->imageUrl))) {
                    // Supprimer l'ancienne photo de imageUrl
                    unlink(public_path($produit->imageUrl));
                }
                
                // Générer un nom de fichier unique
                $filename = time() . '.' . $request->file('imageUri')->getClientOriginalExtension();

                // Définir le chemin de stockage dans le dossier public
                $path = public_path('storage/images');

                // Vérifier si le dossier existe, sinon le créer
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                // Déplacer l'image dans le dossier public
                $request->file('imageUri')->move($path, $filename);

                // Ajouter le chemin de la nouvelle photo dans les données à mettre à jour
                $validated['imageUrl'] = 'storage/images/' . $filename;
            }

            $produit->update(array_merge($validated, [
                'updated_by' => Auth::id(),
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Produit mise à jour avec succès.',
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
     * Remove the specified resource from storage.
     */
    public function destroy(Produit $produit)
    {
        //
    }
}
