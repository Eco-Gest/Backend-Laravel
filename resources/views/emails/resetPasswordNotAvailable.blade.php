<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>

    <!-- Import Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', Arial, sans-serif;
            background-color: #FFF; 
            color: #333; 
            text-align: center;
        }

        .email-logo {
            margin-top: 30px;
        }

        .email-logo img {
            width: 200px;
            height: auto;
        }

        /* Container */
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            overflow: hidden;
        }

        /* Header section */
        .email-header {
            background-color: #006D35; 
            color: white;
            text-align: center;
            padding: 20px;
        }

        .email-header h2 {
            margin: 0;
            font-size: 24px;
        }

        /* Content section */
        .email-body {
            padding: 20px 30px;
        }

        .email-body p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .email-body p a {
            color: #006D35; 
            text-decoration: none;
            font-weight: 600;
        }

        .email-body p a:hover {
            text-decoration: underline;
        }

        /* Button */
        a:hover, a:visited, a:link, a:active
        {
            text-decoration: none;
        }

        .email-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            color: #fff;
            background-color: #006D35; /* Vert principal */
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
        }

        .email-button a {
            color: #fff;
        }

        .email-button:hover {
            background-color: #004b26; 
        }

        /* Footer section */
        .email-footer {
            text-align: center;
            padding: 15px;
            background-color: #F9FAF4;
            font-size: 14px;
            color: #888;
        }

        /* Responsive design */
        @media (max-width: 600px) {
            .email-container {
                margin: 30px 15px;
            }

            .email-body {
                padding: 15px 20px;
            }

            .email-header h2 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-logo">
        <img src="{{ asset('images/logo.png') }}" alt="logo Ecogest">
    </div>

    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h2>Mot de passe oublié ?</h2>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p>
                La fonctionnalité n'est pas encore disponible. Pour changer de mot de passe, veuillez nous contacter à : 
                <a href="mailto:contact@ecogest.org">contact@ecogest.org</a>
            </p>
            <p>
                Si vous n'êtes pas à l'origine de cette demande, veuillez nous le signaler ici :
                <a href="mailto:contact@ecogest.org">contact@ecogest.org</a>
            </p>
            <a href="https://ecogest.org" class="email-button">Visitez notre site</a>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            © {{ date('Y') }} - Ecogest 
        </div>
    </div>
</body>

</html>
