<?php

use App\Http\Controllers\AchatController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CapitalController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\PerteController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\VenteController;

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::prefix('/profile')->group(function () {
            Route::post('/updateUser', [ProfileController::class, 'updateUser']);
            Route::post('/password', [ProfileController::class, 'updatePassword']);
            Route::post('/picture', [ProfileController::class, 'updateProfilePicture']);
        });
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    // AUTHENTIFICATIONS
    Route::post('/register', [AuthController::class, 'register'])->name('inscription');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
    Route::post('/email/verification-notification/{id}', [AuthController::class, 'sendVerificationEmail'])
    ->middleware(['throttle:6,1'])
    ->name('verification.send');


    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user/{id}', [UserController::class, 'getUserById']);
        Route::get('/user/active/{id}', [UserController::class, 'active'])->name('user.active');
        Route::get('/user/desactive/{id}', [UserController::class, 'desactive'])->name('user.desactive');

        // CAPITAL
        Route::prefix('/capital')->group(function() {
            Route::get('/', [CapitalController::class, 'index'])->name('capital.index');
            Route::post('/', [CapitalController::class, 'store'])->name('capital.store');
            Route::get('/{id}', [CapitalController::class, 'show'])->name('capital.show');
            Route::post('/{id}', [CapitalController::class, 'update'])->name('capital.update');
            Route::delete('/{id}', [CapitalController::class, 'destroy'])->name('capital.destroy');
        });

        // ACHAT
        Route::prefix('/achat')->group(function() {
            Route::get('/', [AchatController::class, 'index'])->name('achat.index');
            Route::post('/', [AchatController::class, 'store'])->name('achat.store');
            Route::get('/{id}', [AchatController::class, 'show'])->name('achat.show');
            Route::post('/{id}', [AchatController::class, 'update'])->name('achat.update');
            Route::delete('/{id}', [AchatController::class, 'destroy'])->name('achat.destroy');
        });

        // VENTE
        Route::prefix('/vente')->group(function() {
            Route::get('/', [VenteController::class, 'index'])->name('vente.index');
            Route::post('/', [VenteController::class, 'store'])->name('vente.store');
            Route::get('/{id}', [VenteController::class, 'show'])->name('vente.show');
            Route::post('/{id}', [VenteController::class, 'update'])->name('vente.update');
            Route::delete('/{id}', [VenteController::class, 'destroy'])->name('vente.destroy');
        });

        // DEPENSE
        Route::prefix('/depense')->group(function() {
            Route::get('/', [DepenseController::class, 'index'])->name('depense.index');
            Route::post('/', [DepenseController::class, 'store'])->name('depense.store');
            Route::get('/{id}', [DepenseController::class, 'show'])->name('depense.show');
            Route::post('/{id}', [DepenseController::class, 'update'])->name('depense.update');
            Route::delete('/{id}', [DepenseController::class, 'destroy'])->name('depense.destroy');
        });

        // PERTE
        Route::prefix('/perte')->group(function() {
            Route::get('/', [PerteController::class, 'index'])->name('perte.index');
            Route::post('/', [PerteController::class, 'store'])->name('perte.store');
            Route::get('/{id}', [PerteController::class, 'show'])->name('perte.show');
            Route::post('/{id}', [PerteController::class, 'update'])->name('perte.update');
            Route::delete('/{id}', [PerteController::class, 'destroy'])->name('perte.destroy');
        });

        //PRODUIT
        Route::prefix('/produit')->group(function() {
            Route::get('/', [ProduitController::class, 'index'])->name('produit.index');
            Route::post('/', [ProduitController::class, 'store'])->name('produit.store');
            Route::get('/{id}', [ProduitController::class, 'show'])->name('produit.show');
            Route::post('/{id}', [ProduitController::class, 'update'])->name('produit.update');
            Route::delete('/{id}', [ProduitController::class, 'destroy'])->name('produit.destroy');
        });
    });

    Broadcast::routes(['middleware' => ['auth:sanctum']]);
