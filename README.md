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

# API_KEY

- Entrer dans le container app
- Jouer la commande ci dessous pour entrer en ligne de commande avec tinker
- `php artisan tinker`
- Générer une clé avec la commande ci dessous
- `\Str::random(64)`
- Copier la clé générée dans le .env pour la variable API_KEY
- A chaque appel de l'API, il faudra ajouter cette clé dans les headers pour la varialbe X-API-KEY 

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

# Outil de test pour les mails 
url : https://mailtrap.io/home

# Documentation de l'API

url : http://localhost:8080:api-doc
Fonctionne avec l'API Key dans les headers