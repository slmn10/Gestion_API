<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::get('/storage/{path}/{filename}', function ($path, $filename) {
    // Chemin complet vers le fichier
    $fullPath = storage_path('app/public/' . $path . '/' . $filename);

    // Vérifier si le fichier existe
    if (!File::exists($fullPath)) {
        abort(404);
    }

    // Retourner le fichier avec le bon type MIME
    return Response::file($fullPath);
})->where('path', '.*'); // Accepter tous les sous-dossiers dynamiquement


Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';
