<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <h2 >Un contenu a été signalé par un utilisateur</h2>
    <p>Réception d'un signalement avec les éléments suivants :</p>
    <ul>
      <li><strong>ID du contenu</strong> : {{ $report['ID'] }}</li>
      <li><strong>Titre</strong> : {{ $report['title'] }}</li>
      <li><strong>ID de l'auteur du contenu</strong> : {{ $report['authorID'] }}</li>
      <li><strong>Raison du signalement</strong> : {{ $report['result'] }}</li>
      <li><strong>Contenu</strong> : {{ $report['content'] }}</li>
    </ul>
  </body>
</html>
