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


    <p> <span>Si vous n'êtez pas à l'origine de cette demande, veuillez le signaler à : </span> <a
            href=mailto:"report@ecogest.dev">report@ecogest.dev</a>
    </p>

    <img src="../../assets/logo.png" alt="logo Ecogest">


</body>

</html>