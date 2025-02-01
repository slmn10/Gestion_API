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

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): JsonResponse
    {
        // if ($request->user()->hasVerifiedEmail()) {
        //     return redirect()->intended(
        //         config('app.frontend_url').'/dashboard?verified=1'
        //     );
        // }

        // if ($request->user()->markEmailAsVerified()) {
        //     event(new Verified($request->user()));
        // }

        // return redirect()->intended(
        //     config('app.frontend_url').'/dashboard?verified=1'
        // );

        $user = User::findOrFail($request->id);

        // Vérifie que le hash correspond à celui de l'utilisateur
        if (! hash_equals((string) $request->hash, sha1($user->email))) {
            return response()->json(['message' => 'Email non valide.'], 400);
        }

        // Marquer l'utilisateur comme vérifié
        if ($user->hasVerifiedEmail()) {
            return redirect('https://ilera.vercel.app/auth/login')->with('message', 'Votre email a déjà été vérifié.');
        }

        $user->markEmailAsVerified();

        // Envoyer l'email de bienvenue après la vérification
        Mail::to($user->email)->send(new WelcomeEmail($user));

        return redirect('https://ilera.vercel.app/auth/login')->with('message', 'Votre email a été vérifié avec succès !');

        // if ($request->user()->hasVerifiedEmail()) {
        //     return response()->json([
        //         'message' => 'Email already verified.'
        //     ], 200); // Si l'email est déjà vérifié
        // }

        // if ($request->user()->markEmailAsVerified()) {
        //     event(new Verified($request->user()));
        // }

        // return response()->json([
        //     'message' => 'Email verified successfully.'
        // ], 200); // L'email a été vérifié
    }
}
