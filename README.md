# Ecogest backend API : 


## Créer le fichier .env

`cp .env.example .env`

## Ajouter un mdp dans le .env

## Lancer docker

`docker-compose up -d`
ou
`docker compose up -d`

## Entrer dans le container docker

`docker exec -it app /bin/sh`

* le container s'appelle 'app', car définit ainsi dans le docker-compose.yml

## Installer les dépendances Laravel du composer.json dans le container 

`composer install`

## Générer la clé à l'intérieur du container

`php artisan key:generate`

## Donner les droits à tous les fichiers du container 

`chmod -Rf 777 .`

## Jouer les migrations avec seeding  à l'intérieur du container

`php artisan migrate:fresh --seed`

## Créer un lien pour stocker les images

`php artisan storage:link`

## Adminer est disponible ici :
* http://localhost:9081


## L'api laravel est disponible ici : 
* http://localhost:8080

 ## Jouer les tests 

 `composer test`

