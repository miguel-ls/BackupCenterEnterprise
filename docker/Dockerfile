FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    unzip \
    zip \
    curl \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_sqlite \
        intl \
        zip \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Node.js 22
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar el proyecto
COPY . .

# Evitar advertencia de Git dentro del contenedor
RUN git config --global --add safe.directory /var/www/html

# Instalar dependencias PHP
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction

# Compilar Dashboard Vue
WORKDIR /var/www/html/Dashboard

RUN npm install
RUN npm run build

# Copiar Dashboard compilado a public
RUN cp -R dist/* /var/www/html/public/

# Volver al proyecto
WORKDIR /var/www/html

# Configuración de PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Configuración de Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-enabled/default

# Configuración de Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Crear directorios necesarios
RUN mkdir -p \
    /var/www/html/storage \
    /var/www/html/database \
    /var/log/supervisor

# Permisos
RUN chown -R www-data:www-data /var/www/html

# Puerto HTTP
EXPOSE 80

# Iniciar Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]