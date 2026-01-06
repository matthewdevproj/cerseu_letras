FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Crear usuario no-root para evitar problemas de permisos
RUN useradd -G www-data,root -u 1000 -d /home/laravel laravel && \
    mkdir -p /home/laravel/.composer && \
    chown -R laravel:laravel /home/laravel

# Copiar configuración PHP personalizada (aumentar límites de subida)
COPY php.ini /usr/local/etc/php/conf.d/99-custom.ini

# Cambiar al usuario laravel
USER laravel

# Exponer puerto 9000 para PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
