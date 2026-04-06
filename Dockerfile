# ============================================================
# Dockerfile – Employee Feedback System (PHP 8.2 + Apache)
# ============================================================

# Use the official PHP image with Apache baked in
FROM php:8.2-apache

# ── System dependencies ──────────────────────────────────────
RUN apt-get update && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        zip \
        unzip \
    && rm -rf /var/lib/apt/lists/*

# ── PHP extensions required by the project ───────────────────
# mysqli  – legacy MySQLi API used by some includes
# pdo     – PHP Data Objects base
# pdo_mysql – PDO driver for MySQL (used in config/db.php)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# ── Apache configuration ──────────────────────────────────────
# Enable mod_rewrite so .htaccess rules work correctly
RUN a2enmod rewrite

# Allow .htaccess overrides for the document root
RUN sed -i 's/AllowOverride None/AllowOverride All/g' \
        /etc/apache2/apache2.conf

# ── Application files ─────────────────────────────────────────
# Copy all project source files into the Apache document root
COPY . /var/www/html/

# Remove files that should not be served to the browser
RUN rm -f /var/www/html/Dockerfile \
          /var/www/html/docker-compose.yml \
          /var/www/html/.env \
          /var/www/html/.env.example

# ── Permissions ───────────────────────────────────────────────
# www-data is the user Apache runs as inside the container
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# ── Expose port ───────────────────────────────────────────────
EXPOSE 80
