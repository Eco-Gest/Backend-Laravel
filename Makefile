.PHONY: deploy install

deploy: 
	ssh duha1379@duha1379.odns.fr 'cd ~/repositories/ecogestapi && git pull origin main && make install'

install: vendor/autoload.php .env public/storage public/build/manifest.json
	php artisan cache:clear
	php artisan migrate --force

.env: 
	cp .env.example .env
	php artisan key:generate

public/storage:
	php artisan storage:link

vendor/autoload.php: composer.lock
	composer install 
	touch vendor/autoload.php

public/build/manifest.json: package.json
	npm i 
	npm run build
