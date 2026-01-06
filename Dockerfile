# Use official PHP 8.1 with Apache
FROM php:8.1-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy all files to the container
COPY . /var/www/html/

# Create security headers configuration
RUN echo '<IfModule mod_headers.c>' > /etc/apache2/conf-available/security-headers.conf \
    && echo '    Header set X-Content-Type-Options "nosniff"' >> /etc/apache2/conf-available/security-headers.conf \
    && echo '    Header set X-Frame-Options "SAMEORIGIN"' >> /etc/apache2/conf-available/security-headers.conf \
    && echo '    Header set X-XSS-Protection "1; mode=block"' >> /etc/apache2/conf-available/security-headers.conf \
    && echo '    Header set Referrer-Policy "strict-origin-when-cross-origin"' >> /etc/apache2/conf-available/security-headers.conf \
    && echo '    Header set Content-Security-Policy "default-src '\''self'\''; script-src '\''self'\'' '\''unsafe-inline'\'' https://www.google.com https://www.gstatic.com https://cdn.gravitec.net https://maps.googleapis.com; style-src '\''self'\'' '\''unsafe-inline'\''; frame-ancestors '\''self'\'';"' >> /etc/apache2/conf-available/security-headers.conf \
    && echo '</IfModule>' >> /etc/apache2/conf-available/security-headers.conf \
    && a2enconf security-headers

# Block PHP execution in asset directories (prevent malware)
RUN echo '<DirectoryMatch "^/.*/(js|css|fonts|img)/">' > /etc/apache2/conf-available/block-php-assets.conf \
    && echo '    <FilesMatch "\.php$">' >> /etc/apache2/conf-available/block-php-assets.conf \
    && echo '        Require all denied' >> /etc/apache2/conf-available/block-php-assets.conf \
    && echo '    </FilesMatch>' >> /etc/apache2/conf-available/block-php-assets.conf \
    && echo '</DirectoryMatch>' >> /etc/apache2/conf-available/block-php-assets.conf \
    && a2enconf block-php-assets

# Block access to known malicious directories
RUN echo '<DirectoryMatch "^/.*/(RBw3WC9wE7|2B1sdvjTnX|Qp4XeVAvjj)/">' > /etc/apache2/conf-available/block-malware-dirs.conf \
    && echo '    Require all denied' >> /etc/apache2/conf-available/block-malware-dirs.conf \
    && echo '</DirectoryMatch>' >> /etc/apache2/conf-available/block-malware-dirs.conf \
    && a2enconf block-malware-dirs

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && find /var/www/html -name "*.php" -type f -exec chmod 644 {} \;

# Configure Apache virtual host
RUN echo '<VirtualHost *:80>' > /etc/apache2/sites-available/000-default.conf \
    && echo '    DocumentRoot /var/www/html' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    ServerName localhost' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    <Directory /var/www/html>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        DirectoryIndex index.html index.php' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        Options -Indexes' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]