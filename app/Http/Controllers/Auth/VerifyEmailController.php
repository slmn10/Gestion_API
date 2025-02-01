<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request, $id, $hash)
    {
        // Récupérer l'utilisateur par son ID
        $user = User::find($id);

        // Vérifie que l'utilisateur existe
        if (!$user) {
            return response()->json(['status' => 404, 'message' => 'Utilisateur non trouvé.'], 404);
        }

        // Vérifie que le hash correspond à celui de l'utilisateur
        if (!hash_equals($hash, sha1($user->email))) {
            return response()->json(['status' => 400, 'message' => 'Lien de vérification invalide ou corrompu.'], 400);
        }

        // Vérifie si l'utilisateur a déjà confirmé son email
        if ($user->hasVerifiedEmail()) {
            return redirect('https://ilera.vercel.app/auth/login')->with('message', 'Votre email a déjà été vérifié.');
        }

        // Marquer l'utilisateur comme vérifié
        $user->markEmailAsVerified();
        event(new Verified($user));

        // Envoyer l'email de bienvenue
        Mail::to($user->email)->send(new WelcomeEmail($user));

        // Retourner une réponse JSON de succès
        return redirect('https://ilera.vercel.app/auth/login')->with('message', 'Votre email a été vérifié avec succès !');

    }
}
