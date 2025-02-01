<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur ILERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            color: #333;
        }
        .cta {
            margin: 20px 0;
            text-align: center;
        }
        .cta a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .footer {
            background-color: #f8f9fa;
            color: #666;
            padding: 15px;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Bienvenue sur ILERA!</h1>
        </div>
        <div class="content">
            <p>Bonjour <strong>{{ $user->name }} !</strong>,</p>
            <p>
                Nous sommes ravis de vous accueillir sur <strong>ILERA</strong>, votre nouvelle destination shopping en ligne !
                Préparez-vous à découvrir une expérience d'achat unique avec nos produits soigneusement sélectionnés.
            </p>
            <p class="text-center">
                <span>👕 Mode</span> &nbsp;
                <span>🍜 Alimentation</span> &nbsp;
                <span>🏠 Maison</span> &nbsp;
                <span>📱 High-Tech</span>
            </p>
            <h5>Sur ILERA, vous pouvez :</h5>
            <ul>
                <li>Explorer une large gamme de produits de qualité</li>
                <li>Accéder à des milliers d'articles variés</li>
                <li>Bénéficier de la livraison rapide</li>
                <li>Suivre vos commandes en temps réel</li>
            </ul>
            <div class="cta">
                <a href="#" target="_blank">Commencer mes achats</a>
            </div>
            <p>Notre service client est disponible <strong>7j/7</strong> pour répondre à toutes vos questions.</p>
            <p>À très bientôt sur <strong>ILERA</strong> !</p>
            <p><em>L'équipe ILERA</em></p>
        </div>
        <div class="footer">
            <p>© 2024 ILERA.Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
