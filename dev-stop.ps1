# Development Server Stop Script
# This script stops the Docker development environment

Write-Host "Stopping Saffron Restaurant Development Environment..." -ForegroundColor Yellow

# Check if Docker is installed
try {
    docker --version | Out-Null
} catch {
    Write-Host "ERROR: Docker is not installed or not in PATH" -ForegroundColor Red
    exit 1
}

# Stop Docker containers
docker-compose down

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n✓ Development server stopped successfully!" -ForegroundColor Green
} else {
    Write-Host "`n✗ Failed to stop development server" -ForegroundColor Red
    exit 1
}

