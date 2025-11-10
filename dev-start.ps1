# Development Server Start Script
# This script starts the Docker development environment

Write-Host "Starting Saffron Restaurant Development Environment..." -ForegroundColor Green

# Check if Docker is installed
try {
    $dockerVersion = docker --version
    Write-Host "Docker found: $dockerVersion" -ForegroundColor Cyan
} catch {
    Write-Host "ERROR: Docker is not installed or not in PATH" -ForegroundColor Red
    Write-Host "Please install Docker Desktop from: https://www.docker.com/products/docker-desktop" -ForegroundColor Yellow
    exit 1
}

# Check if docker-compose.yml exists
if (-Not (Test-Path "docker-compose.yml")) {
    Write-Host "ERROR: docker-compose.yml not found!" -ForegroundColor Red
    exit 1
}

# Start Docker containers
Write-Host "`nStarting Docker containers..." -ForegroundColor Yellow
docker-compose up -d

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n✓ Development server started successfully!" -ForegroundColor Green
    Write-Host "`nAccess the website at: http://localhost:8080" -ForegroundColor Cyan
    Write-Host "To view logs, run: docker-compose logs -f" -ForegroundColor Cyan
    Write-Host "To stop the server, run: docker-compose down" -ForegroundColor Cyan
} else {
    Write-Host "`n✗ Failed to start development server" -ForegroundColor Red
    exit 1
}

