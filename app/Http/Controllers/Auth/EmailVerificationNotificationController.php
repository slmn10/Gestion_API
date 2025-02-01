<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request, $id): JsonResponse
    {
        // Récupérer l'utilisateur par son ID
        $user = User::find($id);
        
        if ($user->hasVerifiedEmail()) {
            // return redirect()->intended('/dashboard');

            return response()->json([
                'status' => 'already_verified',
                'message' => 'Your email is already verified.'
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => 'verification-link-sent',
            'message' => 'A new email verification link has been sent.'
        ]);
    }
}
