<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function getUserById($id)
    {
        try {
            // Récupérer un utilisateur par son ID
            $user = User::findOrFail($id);

            // Récupérer les rôles de l'utilisateur
            $roles = $user->getRoleNames(); // Renvoie une collection contenant les noms des rôles
            $primaryRole = $roles->first(); // Rôle principal (on prend le premier)
            $isAdmin = $user->hasAnyRole(['superAdmin', 'adminVestimentaire', 'adminNaturel']);
            $dashboardRoute = $isAdmin ? '/admin/dashboard' : '/client/dashboard';

            // Construire la réponse avec les rôles et autres informations
            $userData = array_merge(
                $user->toArray(),
                [
                    "roles" => $roles, // Ajouter les rôles
                    "role" => $primaryRole, // Rôle principal
                    "redirect_to" => $dashboardRoute // Route de redirection
                ]
            );

            return response()->json([
                "status" => 200,
                "message" => "Utilisateur trouvé avec succès.",
                "data" => $userData
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => "Utilisateur non trouvé.",
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Une erreur s'est produite lors de la récupération de l'utilisateur.",
                "error" => $e->getMessage()
            ], 500);
        }
    }
    
    public function active($id)
    {
        try {
            // Récupérer un utilisateur par son ID
            $user = User::findOrFail($id);

            $user->update([
                'status' => "1",
            ]);

            return response()->json([
                "status" => 200,
                "message" => "Utilisateur activé avec succè."
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => "Utilisateur non trouvé.",
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Une erreur s'est produite lors de la récupération de l'utilisateur.",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function desactive($id)
    {
        try {
            // Récupérer un utilisateur par son ID
            $user = User::findOrFail($id);

            $user->update([
                'status' => "0",
            ]);

            return response()->json([
                "status" => 200,
                "message" => "Utilisateur désactivé avec succè."
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => "Utilisateur non trouvé.",
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Une erreur s'est produite lors de la récupération de l'utilisateur.",
                "error" => $e->getMessage()
            ], 500);
        }
    }

}
