<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../../css/app.css">

</head>

<body>
    <h2>Mot de passe oublié ?</h2>

    <!-- lien vers le site web flutter pour réinitialiser le mdp  -->
    <a href="http://localhost:3000/reset-password?token={{ $data['token'] }}">
        <button>Réinitialisez mon mot de passe</button>
    </a>

    <p> <span>
        Attention, le lien expire dans 15 minutes.
        </span></p>
    <p> <span>Si vous n'êtez pas à l'origine de cette demande, veuillez le signaler à : </span> <a
            href=mailto:"report@ecogest.dev">report@ecogest.dev</a>
    </p>

    <img src="{{ asset('images/logo.png') }}" alt="logo Ecogest">

    <style>

        body {
            margin-left: 6%;
            margin-right: 6%;
        }

        button {
            background-color: rgb(0,109, 53);
            padding: 10px 20px;
            border: 1px solid rgb(0,109, 53);
            border-radius: 10px;
            color: #FFF;
        }
        a {
            text-decoration: none;
            color: rgb(0,109, 53);
            &:visited {
                color: rgb(0,109, 53);

            }
        }

        img {
            width: 12%;
        }

    </style>

</body>

</html>