FROM php:8.2-fpm

# 1. CONFIGURAR REPOSITORIOS Y DEPENDENCIAS BASE
RUN apt-get update && apt-get install -y \
    ca-certificates \
    curl \
    gnupg \
    lsb-release \
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_22.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list \
    && apt-get update

# 2. INSTALAR DEPENDENCIAS DEL SISTEMA Y EXTENSIONES PHP
RUN apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    nodejs \
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

# 3. ACTUALIZAR NPM A LA ÚLTIMA VERSIÓN
RUN npm install -g npm@latest \
    && npm cache clean --force

# 4. INSTALAR COMPOSER DESDE LA IMAGEN OFICIAL
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 5. CONFIGURAR DIRECTORIO DE TRABAJO
WORKDIR /var/www/html

# 6. CREAR USUARIO NO-ROOT PARA EVITAR PROBLEMAS DE PERMISOS
RUN useradd -G www-data,root -u 1000 -d /home/laravel laravel && \
    mkdir -p /home/laravel/.composer && \
    chown -R laravel:laravel /home/laravel

# 7. COPIAR CONFIGURACIÓN PHP PERSONALIZADA
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# 8. CAMBIAR AL USUARIO LARAVEL
USER laravel

# 9. EXPONER PUERTO 9000 PARA PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
