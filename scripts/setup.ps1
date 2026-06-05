$ErrorActionPreference = "Stop"

if (-not (Get-Command laravel -ErrorAction SilentlyContinue)) {
    throw "Laravel installer was not found. Run: composer global require laravel/installer"
}

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    throw "Docker was not found. Install Docker Desktop before continuing."
}

if (-not (Test-Path "api/artisan")) {
    Write-Host "Creating the Laravel API project..."
    laravel new api
}

if (-not (Test-Path "api/routes/api.php")) {
    Write-Host "Enabling Laravel API routes..."
    Push-Location api
    php artisan install:api --no-interaction
    Pop-Location
}

if (-not (Test-Path "api/.env")) {
    Copy-Item "docker/api/.env.docker.example" "api/.env"
}

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
}

Write-Host "Building and starting containers..."
docker compose up -d --build

docker compose exec api php artisan key:generate --force
docker compose exec api php artisan migrate --force

Write-Host "Application available at http://localhost:8080"
