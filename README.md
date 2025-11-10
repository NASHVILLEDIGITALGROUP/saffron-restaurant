# Saffron Restaurant Website

Website for Saffron The Indian Kitchen - An Indian restaurant in Nashville, TN located at Assembly Food Hall.

## Quick Start

### Using Docker (Recommended)

1. **Install Docker Desktop** from https://www.docker.com/products/docker-desktop

2. **Start the development server:**
   ```powershell
   .\dev-start.ps1
   ```
   Or manually:
   ```bash
   docker-compose up -d
   ```

3. **Access the website:**
   - Open http://localhost:8080 in your browser

4. **Stop the server:**
   ```powershell
   .\dev-stop.ps1
   ```
   Or manually:
   ```bash
   docker-compose down
   ```

### Alternative: Local PHP Server

If you have PHP installed:
```bash
php -S localhost:8000
```

## Documentation

For detailed setup instructions, see [DEVELOPMENT.md](DEVELOPMENT.md)

## Project Structure

- `index.html` - Main homepage
- `contact.html` - Contact page
- `mail_send.php` - Contact form handler
- `css/` - Stylesheets
- `js/` - JavaScript files
- `img/` - Images and assets

## Development Scripts

- `dev-start.ps1` - Start development server
- `dev-stop.ps1` - Stop development server
- `dev-logs.ps1` - View server logs

## Requirements

- PHP 7.4+ (or Docker)
- Apache/Nginx web server (or Docker)
- Docker Desktop (for Docker setup)
