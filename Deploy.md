# Guide de Déploiement Production (OVH + Docker)

## 1. Préparation du VPS

1. Connectez-vous à votre VPS.
2. Installez Docker et Docker Compose.
3. Créez le dossier de destination :
   ```bash
   mkdir -p /var/www/ecogest/backend
   chown -R $USER:$USER /var/www/ecogest
   ```

## 2. Configuration GitHub Secrets

Allez dans votre repository GitHub -> Settings -> Secrets and variables -> Actions -> **New repository secret**.

Ajoutez les secrets suivants :

| Nom Secret        | Description                                                         |
| ----------------- | ------------------------------------------------------------------- |
| `VPS_IP`          | Adresse IP de votre VPS OVH                                         |
| `VPS_USER`        | Nom d'utilisateur SSH (ex: debian, ubuntu, ou root)                 |
| `SSH_PRIVATE_KEY` | Contenu de votre clé privée SSH (celle qui permet d'accéder au VPS) |
| `APP_NAME`        | Nom de l'app (ex: Ecogest)                                          |
| `APP_KEY`         | Clé Laravel (Générez-en une avec `php artisan key:generate --show`) |
| `APP_URL`         | URL publique (ex: https://api.mondomaine.com)                       |
| `DB_HOST`         | `host.docker.internal` (votre PGSQL est sur l'hôte, hors Docker)    |
| `DB_PORT`         | Port PostgreSQL (ex: 5432)                                          |
| `DB_DATABASE`     | Nom de la base de données                                           |
| `DB_USERNAME`     | Utilisateur BDD                                                     |
| `DB_PASSWORD`     | Mot de passe BDD                                                    |

## 3. Configuration SSL (Nginx Host)

Le conteneur Docker expose le port **80**. Il est recommandé d'utiliser Nginx directement sur le VPS (hors Docker) comme reverse proxy pour gérer le SSL facilement avec Certbot.

Exemple de config `/etc/nginx/sites-available/mon-domaine.conf` sur le VPS :

```nginx
server {
    server_name api.mon-domaine.com;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Puis lancez : `sudo certbot --nginx -d api.mon-domaine.com`
