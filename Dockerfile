# Imagen base con PHP 8.4 + FPM
FROM php:8.4-fpm AS base

# Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar Node.js 20 para compilar el frontend
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Directorio de trabajo
WORKDIR /var/www/html

# ── Fase de dependencias ──
FROM base AS dependencies

# Copiar archivos de dependencias primero (para cache de Docker layers)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY package.json package-lock.json* ./
RUN npm ci --production=false

# ── Fase de construcción ──
FROM dependencies AS build

# Copiar todo el código
COPY . .

# Re-ejecutar scripts de Composer (post-install)
RUN composer dump-autoload --optimize

# Compilar assets del frontend (Vite + Tailwind)
RUN npm run build

# Limpiar node_modules (ya no se necesitan en producción)
RUN rm -rf node_modules

# ── Imagen final ──
FROM base AS production

WORKDIR /var/www/html

# Copiar la aplicación completa desde la fase de build
COPY --from=build /var/www/html /var/www/html

# Copiar configuraciones del servidor
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Crear directorios necesarios y permisos
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Railway usa la variable PORT (por defecto 80)
EXPOSE 80

CMD ["/start.sh"]
