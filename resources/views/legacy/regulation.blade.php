@extends('layouts.app')

@section('content')
        <div class="container">
        <h1 class="text-center my-4 text-success">Bienvenue chez Ecogest</h1>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h2 class="text-success">Présentation de l'application</h2>
                <p class="text-muted">Ecogest est une application développée dans un objectif purement écologique et sans
                    but lucratif.
                    Elle vise à aider les utilisateurs à mieux gérer leurs ressources et à adopter des pratiques plus
                    respectueuses de l’environnement.</p>

                <h2 class="text-success">Respect de la vie privée</h2>
                <p class="text-muted">Ecogest s'engage pleinement à protéger votre vie privée :</p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Aucune publicité n'est affichée sur l'application.</li>
                    <li class="list-group-item">Vos données ne sont ni revendues ni réutilisées.</li>
                    <li class="list-group-item">Aucun cookie n'est utilisé pour suivre votre activité.</li>
                </ul>

                <h2 class="text-success">Suppression du compte</h2>
                <p class="text-muted">Vous avez la possibilité de supprimer définitivement votre compte à tout moment dès sa
                    création.
                    Cette suppression entraîne l’effacement immédiat et irréversible de toutes vos données.</p>

                <h2 class="text-success">Engagement des créateurs</h2>
                <p class="text-muted">Ecogest a été conçu par <strong class="text-dark">Augustin SEGUIN</strong> et <strong
                        class="text-dark">Eleonore EUZENES</strong>, deux développeurs engagés dans la protection de
                    l’environnement et le respect de la vie privée des utilisateurs.</p>

                <h2 class="text-success">Acceptation des conditions</h2>
                <p class="text-muted">En utilisant Ecogest, vous acceptez ces conditions générales d'utilisation. Si vous ne
                    les acceptez pas, nous vous invitons à ne pas utiliser l’application.</p>
            </div>
        </div>
    </div>
@endsection