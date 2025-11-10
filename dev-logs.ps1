# Development Server Logs Script
# This script shows Docker container logs

Write-Host "Viewing Saffron Restaurant Development Environment Logs..." -ForegroundColor Yellow
Write-Host "Press Ctrl+C to exit`n" -ForegroundColor Gray

# Check if Docker is installed
try {
    docker --version | Out-Null
} catch {
    Write-Host "ERROR: Docker is not installed or not in PATH" -ForegroundColor Red
    exit 1
}

# Show logs
docker-compose logs -f

