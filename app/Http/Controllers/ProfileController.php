<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Mettre à jour les informations de l'utilisateur.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateUser(Request $request): JsonResponse
    {
        try {
            // Validation des données
            $validatedData = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
                'phone' => ['nullable', 'string', 'max:20'],
                'profile_picture' => ['nullable', 'image', 'max:2048'], // Photo de profil est optionnelle
            ], [
                'name.required' => 'Le champ nom est requis.',
                'name.string' => 'Le champ nom doit être une chaîne de caractères.',
                'name.max' => 'Le champ nom ne doit pas dépasser 255 caractères.',

                'username.string' => 'Le champ nom d’utilisateur doit être une chaîne de caractères.',
                'username.max' => 'Le champ nom d’utilisateur ne doit pas dépasser 255 caractères.',
                'username.unique' => 'Ce nom d’utilisateur est déjà utilisé.',

                'email.required' => 'Le champ email est requis.',
                'email.string' => 'Le champ email doit être une chaîne de caractères.',
                'email.email' => 'Le champ email doit être une adresse email valide.',
                'email.max' => 'Le champ email ne doit pas dépasser 255 caractères.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',

                'phone.string' => 'Le champ téléphone doit être une chaîne de caractères.',
                'phone.max' => 'Le champ téléphone ne doit pas dépasser 20 caractères.',

                'profile_picture.image' => 'Le fichier doit être une image.',
                'profile_picture.max' => 'La photo ne doit pas dépasser 2 Mo.',
            ]);

            // Récupérer l'utilisateur connecté
            $user = Auth::user();

            $url = "https://ilera-naturals.raedd-cameroun.org/";

            // Traitement de la photo de profil si elle est présente
            if ($request->hasFile('profile_picture')) {
                // Vérifier si une photo de profil existe déjà
                if ($user->profil && file_exists(public_path($user->profil))) {
                    // Supprimer l'ancienne photo de profil
                    unlink(public_path($user->profil));
                }

                // Générer un nom de fichier unique
                $filename = time() . '.' . $request->file('profile_picture')->getClientOriginalExtension();

                // Définir le chemin de stockage dans le dossier public
                $path = public_path('images/profile_pictures');

                // Vérifier si le dossier existe, sinon le créer
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                // Déplacer l'image dans le dossier public
                $request->file('profile_picture')->move($path, $filename);

                // Ajouter le chemin de la nouvelle photo dans les données à mettre à jour
                $validatedData['profil'] = $url . 'images/profile_pictures/' . $filename;
            }

            // Mettre à jour les informations de l'utilisateur
            $user->update($validatedData);

            // Récupérer les rôles de l'utilisateur
            $roles = $user->getRoleNames();
            $primaryRole = $roles->first(); // Rôle principal
            $isAdmin = $user->hasAnyRole(['superAdmin', 'adminVestimentaire', 'adminNaturel']);
            $dashboardRoute = $isAdmin ? '/admin/dashboard' : '/client/dashboard';

            // Construire la réponse
            $updatedUserData = array_merge(
                $user->toArray(),
                [
                    "roles" => $roles, // Ajouter les rôles
                    "role" => $primaryRole, // Rôle principal
                    "redirect_to" => $dashboardRoute, // Route de redirection
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès.',
                'data' => $updatedUserData,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturer les erreurs de validation
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Gérer d'autres exceptions inattendues
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue.',
                'error' => $e->getMessage(), // Retirer ce message en production
            ], 500);
        }
    }



    /**
     * Mettre à jour la photo de profil.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfilePicture(Request $request): JsonResponse
    {
        try {
            // Validation des données
            $validatedData = $request->validate([
                'profile_picture' => ['required', 'image', 'max:2048'],
            ], [
                'profile_picture.required' => 'La photo de profil est requise.',
                'profile_picture.image' => 'Le fichier doit être une image.',
                'profile_picture.max' => 'La photo ne doit pas dépasser 2 Mo.',
            ]);

            // Générer un nom de fichier unique
            $filename = time() . '.' . $request->file('profile_picture')->getClientOriginalExtension();

            // Définir le chemin de stockage dans le dossier public
            $path = public_path('images/profile_pictures');

            // Vérifier si le dossier existe, sinon le créer
            if (!file_exists($path)) {
                mkdir($path, 0777, true); // Crée le dossier avec les permissions appropriées
            }

            // Déplacer l'image dans le dossier public
            $request->file('profile_picture')->move($path, $filename);

            // Mettre à jour le chemin de l'image dans la base de données
            $user = Auth::user();
            $user->update(['profil' => 'images/profile_pictures/' . $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès.',
                'path' => 'images/profile_pictures/' . $filename
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturer les erreurs de validation
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Gérer les erreurs générales
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue.',
                'error' => $e->getMessage() // Retirer ce message en production pour éviter d'exposer les détails
            ], 500);
        }
    }


    /**
     * Mettre à jour le mot de passe.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            // Validation des données
            $validatedData = $request->validate([
                'current_password' => ['required'],
                'password' => ['required', 'confirmed', 'min:8'], // Minimum de 8 caractères
            ], [
                'current_password.required' => 'Le mot de passe actuel est requis.',
                'password.required' => 'Le nouveau mot de passe est requis.',
                'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            ]);

            $user = Auth::user();

            // Vérifier si le mot de passe actuel est correct
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'errors' => ['current_password' => ['Le mot de passe actuel est incorrect.']]
                ], 422);
            }

            // Mettre à jour le mot de passe
            $user->update(['password' => Hash::make($request->password)]);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe mis à jour avec succès.'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Gérer les autres erreurs possibles
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue.',
                'error' => $e->getMessage() // Retirer ce message en production pour éviter d'exposer les détails
            ], 500);
        }
    }

}
