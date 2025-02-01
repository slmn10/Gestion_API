<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe</title>
</head>
<body>
    <h1>Réinitialisation de votre mot de passe</h1>
    <p>Bonjour,</p>
    <p>Nous avons reçu une demande pour réinitialiser le mot de passe de votre compte. Cliquez sur le lien ci-dessous pour le réinitialiser :</p>
    <a href="{{ url('password/reset', $token) }}">Réinitialiser mon mot de passe</a>
    <p>Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer ce message.</p>
</body>
</html>
