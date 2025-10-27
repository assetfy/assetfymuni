# Etapa 1: Build de Node/Vite
FROM node:18 AS node-builder

WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Etapa 2: PHP con SQL Server
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl wget gnupg2 apt-transport-https \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    zip unzip nginx ca-certificates lsb-release \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Microsoft ODBC Driver 18 para SQL Server
RUN curl https://packages.microsoft.com/keys/microsoft.asc | tee /etc/apt/trusted.gpg.d/microsoft.asc \
    && curl https://packages.microsoft.com/config/debian/11/prod.list | tee /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18 unixodbc-dev \
    && echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >> ~/.bashrc

# Instalar extensiones PHP base
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar extensiones sqlsrv y pdo_sqlsrv
RUN pecl install sqlsrv-5.12.0 pdo_sqlsrv-5.12.0 \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Variables de entorno para build
ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_KEY=base64:f5f3JdOVRKtYtt9Xir3o3/yq6/mdC0Nm54RQpyytezc= \
    APP_URL=https://laradev-production.up.railway.app/ \
LOG_CHANNEL=stack \
LOG_DEPRECATIONS_CHANNEL=null \
LOG_LEVEL=debug \
DEBUGBAR_ENABLED=false \
DB_CONNECTION=sqlsrv \
DB_HOST=18.231.5.13 \
DB_PORT=1433 \
DB_DATABASE=assetfymuni \
DB_USERNAME=mvplara \
DB_PASSWORD=l4r4Mvp! \
DB_ENCRYPT=yes \
DB_TRUST_SERVER_CERTIFICATE=true


# Copiar composer files
COPY composer.json composer.lock ./

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copiar código fuente
COPY . .

# Copiar assets compilados
COPY --from=node-builder /app/public/build ./public/build

# Dump autoload
RUN composer dump-autoload --optimize --no-dev || true

# Crear directorios y permisos
RUN mkdir -p /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/storage/framework/cache \
    /var/www/storage/logs \
    /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Configurar Nginx
RUN echo 'server {\n\
    listen 8080;\n\
    root /var/www/public;\n\
    index index.php;\n\
    \n\
    location / { \n\
        try_files $uri $uri/ /index.php?$query_string; \n\
    }\n\
    \n\
    location ~ \.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;\n\
        include fastcgi_params;\n\
        fastcgi_read_timeout 300;\n\
    }\n\
}' > /etc/nginx/sites-available/default

EXPOSE 8080

# Script de inicio
RUN echo '#!/bin/bash\n\
set -e\n\
echo "Starting PHP-FPM..."\n\
php-fpm -D\n\
echo "Clearing caches..."\n\
php artisan config:clear 2>/dev/null || true\n\
php artisan cache:clear 2>/dev/null || true\n\
php artisan view:clear 2>/dev/null || true\n\
echo "Caching configuration..."\n\
php artisan config:cache 2>/dev/null || true\n\
echo "Starting Nginx..."\n\
nginx -g "daemon off;"' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
