# Development Environment Setup Guide

This guide will help you set up a local development environment for the Saffron Restaurant website.

## Prerequisites

Choose one of the following options:

### Option 1: Docker (Recommended)

1. **Install Docker Desktop**
   - Download from: https://www.docker.com/products/docker-desktop
   - Install and start Docker Desktop
   - Verify installation: `docker --version`

2. **Start Development Server**
   ```bash
   docker-compose up -d
   ```

3. **Access the Website**
   - Open your browser and navigate to: http://localhost:8080

4. **Stop Development Server**
   ```bash
   docker-compose down
   ```

5. **View Logs**
   ```bash
   docker-compose logs -f
   ```

### Option 2: Local PHP Server

1. **Install PHP 7.4 or higher**
   - Download from: https://www.php.net/downloads.php
   - Or use XAMPP: https://www.apachefriends.org/
   - Add PHP to your system PATH

2. **Start PHP Built-in Server**
   ```bash
   cd saffron-restaurant
   php -S localhost:8000
   ```

3. **Access the Website**
   - Open your browser and navigate to: http://localhost:8000

### Option 3: XAMPP/WAMP/MAMP

1. **Install XAMPP** (Windows) or **MAMP** (Mac) or **WAMP** (Windows)
   - XAMPP: https://www.apachefriends.org/
   - MAMP: https://www.mamp.info/
   - WAMP: https://www.wampserver.com/

2. **Copy Project Files**
   - Copy the `saffron-restaurant` folder to:
     - XAMPP: `C:\xampp\htdocs\saffron-restaurant`
     - MAMP: `/Applications/MAMP/htdocs/saffron-restaurant`
     - WAMP: `C:\wamp64\www\saffron-restaurant`

3. **Start Apache Server**
   - Use the XAMPP/MAMP/WAMP control panel

4. **Access the Website**
   - Navigate to: http://localhost/saffron-restaurant

## Development Workflow

### Using Docker (Recommended)

1. **Start the development server:**
   ```bash
   docker-compose up -d
   ```

2. **View running containers:**
   ```bash
   docker-compose ps
   ```

3. **Access container shell:**
   ```bash
   docker-compose exec web bash
   ```

4. **Rebuild container after Dockerfile changes:**
   ```bash
   docker-compose up -d --build
   ```

5. **Stop the server:**
   ```bash
   docker-compose down
   ```

### File Structure

```
saffron-restaurant/
├── css/              # Stylesheets
├── js/               # JavaScript files
├── img/              # Images
├── fonts/            # Font files
├── vendor/           # Third-party libraries
├── index.html        # Main homepage
├── contact.html      # Contact page
├── mail_send.php     # Contact form handler
├── Dockerfile        # Docker configuration
├── docker-compose.yml # Docker Compose configuration
└── .env.example      # Environment variables template
```

## Configuration

1. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Update `.env` file** with your configuration values

## Testing

### Test Contact Form

1. Navigate to: http://localhost:8080/contact.html
2. Fill out the contact form
3. Submit and verify email is sent (check logs)

### Check PHP Errors

- With Docker: Check logs using `docker-compose logs -f`
- With local PHP: Errors will display in terminal
- With XAMPP/MAMP: Check Apache error logs

## Troubleshooting

### Port Already in Use

If port 8080 is already in use, change it in `docker-compose.yml`:
```yaml
ports:
  - "8081:80"  # Change 8080 to 8081
```

### PHP Extensions Missing

If you encounter missing PHP extensions:
- **Docker**: They're already included in the Dockerfile
- **Local PHP**: Install required extensions via package manager

### Permission Issues

- **Docker**: Permissions are handled automatically
- **Local**: Ensure web server has read access to all files

## Production Deployment

For production deployment, use the Dockerfile:
```bash
docker build -t saffron-restaurant .
docker run -d -p 80:80 saffron-restaurant
```

## Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [PHP Documentation](https://www.php.net/docs.php)
- [Apache Documentation](https://httpd.apache.org/docs/)

