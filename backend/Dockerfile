FROM php:8.4-fpm

WORKDIR /var/www/html

# System dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    curl \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql gd zip \
 && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Caddy (inside PHP container, Option 2)
RUN apt-get update && apt-get install -y --no-install-recommends \
    gnupg \
    curl \
    debian-keyring \
    debian-archive-keyring \
    apt-transport-https \
 && curl -1sLf https://dl.cloudsmith.io/public/caddy/stable/gpg.key \
      | gpg --dearmor -o /usr/share/keyrings/caddy.gpg \
 && curl -1sLf https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt \
      | tee /etc/apt/sources.list.d/caddy-stable.list \
 && apt-get update \
 && apt-get install -y --no-install-recommends caddy \
 && rm -rf /var/lib/apt/lists/*

# Copy application
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Copy backend Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

# Expose HTTP
EXPOSE 80

# Start both services
CMD sh -c "php-fpm -D && caddy run --config /etc/caddy/Caddyfile --adapter caddyfile"
