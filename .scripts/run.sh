#!/bin/sh

# Build app image
docker-compose build

# Run app 
docker-compose up -d

# Run config initial
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan jwt:secret
docker-compose exec app php artisan config:cache

docker-compose exec app php artisan migrate:refresh --seed