#!/usr/bin/env sh
set -eu

command -v laravel >/dev/null 2>&1 || {
  echo "Laravel installer was not found. Run: composer global require laravel/installer" >&2
  exit 1
}

command -v docker >/dev/null 2>&1 || {
  echo "Docker was not found. Install Docker before continuing." >&2
  exit 1
}

if [ ! -f api/artisan ]; then
  echo "Creating the Laravel API project..."
  laravel new api
fi

if [ ! -f api/routes/api.php ]; then
  echo "Enabling Laravel API routes..."
  (
    cd api
    php artisan install:api --no-interaction
  )
fi

[ -f api/.env ] || cp docker/api/.env.docker.example api/.env
[ -f .env ] || cp .env.example .env

echo "Building and starting containers..."
docker compose up -d --build

docker compose exec api php artisan key:generate --force
docker compose exec api php artisan migrate --force

echo "Application available at http://localhost:8080"
