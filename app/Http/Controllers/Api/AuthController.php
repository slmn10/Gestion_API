<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // * Inscription d'un utilisateur
    public function register(Request $request)
    {
        // Validation des données
        try {
            $payload = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', 'min:6'],
            ], [
                'name.required' => 'Le champ nom est requis.',
                'name.string' => 'Le nom doit être une chaîne de caractères.',
                'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
                'email.required' => 'Le champ email est requis.',
                'email.email' => 'L\'email doit être une adresse valide.',
                'email.lowercase' => 'L\'email doit être en minuscules.',
                'email.unique' => 'Cet email est déjà utilisé.',
                'password.required' => 'Le champ mot de passe est requis.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',
                'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "status" => 422,
                "message" => "Erreur de validation",
                "errors" => $e->errors()
            ], 422);
        }

        try {
            // Hacher le mot de passe
            $payload["password"] = Hash::make($payload["password"]);

            $payload["role"] = 'client';

            // Création de l'utilisateur
            $user = User::create($payload);

            // Assigner le rôle `client`
            $user->assignRole('client');

            // Création d'un client associé
            Client::create(['user_id' => $user->id]);

            // Authentifier l'utilisateur
            Auth::login($user);

            // Envoi de l'email de vérification
            $user->sendEmailVerificationNotification();

            // Si vous souhaitez envoyer un email de bienvenue ou de confirmation, vous pouvez le faire ici :
            // Mail::to($user->email)->send(new WelcomeEmail($user));

            return response()->json([
                "status" => 200,
                "message" => "Compte créé avec succès!",
                "user" => $user
            ]);
        } catch (\Exception $err) {
            // Enregistrer les erreurs dans les logs
            Log::error("Erreur lors de l'inscription: " . $err->getMessage(), [
                'exception' => $err,
                'payload' => $payload
            ]);

            return response()->json([
                "status" => 500,
                "message" => "Une erreur s'est produite!",
                "error" => $err->getMessage()
            ], 500);
        }
    }

    public function verify(Request $request)
    {
        $user = User::findOrFail($request->id);

        // Vérifie que le hash correspond à celui de l'utilisateur
        if (! hash_equals((string) $request->hash, sha1($user->email))) {
            return response()->json(['message' => 'Email non valide.'], 400);
        }

        // Marquer l'utilisateur comme vérifié
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Votre email a déjà été vérifié.']);
        }

        $user->markEmailAsVerified();

        // Envoyer l'email de bienvenue après la vérification
        Mail::to($user->email)->send(new WelcomeEmail($user));

        return response()->json([
            'status' => 200,
            'message' => 'Votre email a été vérifié avec succès !',
            'user' => $user
        ]);
    }

    // * Connexion de l'utilisateur
    public function login(Request $request)
    {
        $payload = $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        try {
            $user = User::where("email", $payload["email"])->first();
            if ($user && Hash::check($payload["password"], $user->password)) {
                // Générer le token pour l'utilisateur
                $token = $user->createToken("web")->plainTextToken;

                // Récupérer les rôles de l'utilisateur (si un utilisateur peut avoir plusieurs rôles)
                $roles = $user->getRoleNames(); // Renvoie une collection contenant les noms des rôles

                // Déterminer la redirection en fonction du rôle (si un seul rôle est attendu)
                $primaryRole = $roles->first(); // On prend le premier rôle

                // Déterminer la redirection en fonction des rôles
                $isAdmin = $user->hasAnyRole(['superAdmin', 'adminVestimentaire', 'adminNaturel']);
                $dashboardRoute = $isAdmin ? '/admin/dashboard' : '/client/dashboard';

                // Construire la réponse avec les données utilisateur et le rôle
                $authRes = array_merge(
                    $user->toArray(),
                    [
                        "roles" => $roles, // Ajouter les rôles
                        "role" => $primaryRole, // Rôle principal (si besoin)
                        "token" => $token,
                        "redirect_to" => $dashboardRoute
                    ]
                );

                return response()->json([
                    "status" => 200,
                    "user" => $authRes,
                    "message" => "Connexion réussie!"
                ]);
            }

            return response()->json(["status" => 401, "message" => "Identifiants invalides."], 401);
        } catch (\Exception $err) {
            Log::error("Erreur lors de la connexion: " . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Une erreur s'est produite!"], 500);
        }
    }


    // * Déconnexion
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return ["status" => 200, "message" => "Déconnexion réussie!"];
        } catch (\Exception $err) {
            Log::error("Erreur lors de la déconnexion: " . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Une erreur s'est produite!"], 500);
        }
    }

    /**
     * Envoyer le lien de vérification de l'email.
     */
    public function sendVerificationEmail(Request $request, $id)
    {
        // Récupérer l'utilisateur par son ID
        $user = User::find($id);

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'already_verified',
                'message' => 'Votre email est déjà vérifié.'
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => 'verification-link-sent',
            'message' => 'Un nouveau lien de vérification a été envoyé.'
        ]);
    }

    public function loginApp(Request $request)
    {
        $payload = $request->validate([
            "email" => 'required|email',
            "password" => 'required',
        ]);

        try {
            $user = User::where("email", $payload["email"])->first();

            if ($user) {
                // Vérifier si l'email a été vérifié
                if (!$user->email_verified_at) {
                    return response()->json([
                        "status" => 403,
                        "user_id" => $user->id,
                        "message" => "Votre email n'a pas encore été vérifié. Veuillez vérifier votre boîte mail."
                    ], 403);
                }

                // Vérifier le mot de passe
                if (Hash::check($payload["password"], $user->password)) {
                    // Générer le token pour l'utilisateur
                    $token = $user->createToken("web")->plainTextToken;

                    // Récupérer les rôles de l'utilisateur (si un utilisateur peut avoir plusieurs rôles)
                    $roles = $user->getRoleNames(); // Renvoie une collection contenant les noms des rôles

                    // Déterminer le rôle principal (le premier dans la liste)
                    $primaryRole = $roles->first();

                    // Déterminer la redirection en fonction des rôles
                    $isAdmin = $user->hasAnyRole(['superAdmin', 'adminVestimentaire', 'adminNaturel']);
                    $dashboardRoute = $isAdmin ? '/admin/dashboard' : '/client/dashboard';

                    // Construire la réponse avec les données utilisateur et le rôle
                    $authRes = array_merge(
                        $user->toArray(),
                        [
                            "roles" => $roles, // Ajouter les rôles
                            "role" => $primaryRole, // Rôle principal (si besoin)
                            "token" => $token,
                            "redirect_to" => $dashboardRoute
                        ]
                    );

                    return response()->json([
                        "status" => 200,
                        "user" => $authRes,
                        "message" => "Connexion réussie!"
                    ]);
                }

                return response()->json(["status" => 401, "message" => "Identifiants invalides."], 401);
            }

            return response()->json(["status" => 404, "message" => "Utilisateur non trouvé."], 404);
        } catch (\Exception $err) {
            Log::error("Erreur lors de la connexion: " . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Une erreur s'est produite!"], 500);
        }
    }
}
