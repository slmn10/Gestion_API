<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        // Valider les données d'entrée
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Le champ email est requis.',
            'password.required' => 'Le champ mot de passe est requis.',
        ]);

        // Vérifier les identifiants
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Les identifiants sont incorrects.',
            ], 401);
        }

        // Authentifier l'utilisateur
        Auth::login($user);

        // Créer un token pour l'utilisateur (optionnel, selon tes besoins)
        $token = $user->createToken('MyAppToken')->plainTextToken;

        $dashboardRoute = $user->hasRole('admin') ? '/admin/dashboard' : '/client/dashboard';

        $authRes = array_merge($user->toArray(), ["token" => $token, "redirect_to" => $dashboardRoute]);

        // Retourner la réponse avec le token
        return response()->json([
            'message' => 'Connexion réussie',
            'data' => $authRes,
            'token' => $token
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
