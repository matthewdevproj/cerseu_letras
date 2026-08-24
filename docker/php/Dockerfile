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
# GD se compila con --with-webp. Sin esa bandera imagewebp() no existe,
# OptimizadorImagen lanza y su plan B guarda la imagen subida tal cual: sin
# redimensionar ni comprimir. El fallo es silencioso -prefiere publicar una
# imagen pesada antes que dar error- y solo se nota en el peso de las paginas.
RUN apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
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

# 8. PREPARAR vendor/ Y node_modules/ CON EL DUEÑO CORRECTO
# Un volumen nombrado se inicializa copiando lo que la imagen tenga en esa ruta,
# incluido el propietario. Sin estas carpetas, Docker las crea vacías y como
# root, y el usuario `laravel` no puede escribir dentro: `composer install`
# muere con «/var/www/html/vendor/... does not exist and could not be created».
RUN mkdir -p /var/www/html/vendor /var/www/html/node_modules     && chown -R laravel:laravel /var/www/html

# 9. CAMBIAR AL USUARIO LARAVEL
USER laravel

# 10. EXPONER PUERTO 9000 PARA PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
