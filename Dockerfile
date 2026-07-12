FROM php:8.2-cli

# ==========================
# Install system packages
# ==========================
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    zip \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        intl \
        mbstring \
        bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ==========================
# Install Composer
# ==========================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ==========================
# Install Node.js 22
# ==========================
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# ==========================
# Working Directory
# ==========================
WORKDIR /var/www

# ==========================
# Copy seluruh project
# ==========================
COPY . .

# ==========================
# Install PHP Dependency
# ==========================
RUN composer install --no-interaction

# ==========================
# Install Node Dependency
# ==========================
RUN npm install

# ==========================
# Permission
# ==========================
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ==========================
# Port
# ==========================
EXPOSE 8000

# ==========================
# Run Laravel
# ==========================
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
