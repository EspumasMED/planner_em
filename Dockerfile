# Usa Ubuntu 22.04 como imagen base
FROM ubuntu:22.04

# Evita interacciones durante la instalación de paquetes
ENV DEBIAN_FRONTEND=noninteractive

# Actualiza el sistema e instala paquetes necesarios, incluyendo nano
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y bash git sudo openssh-client \
    libxml2-dev libonig-dev autoconf gcc g++ make npm \
    libfreetype6-dev libjpeg-turbo8-dev libpng-dev libzip-dev ssmtp \
    curl php-cli php-mbstring php-xml php-zip unzip \
    nano

# Instala PHP 8.1 y extensiones requeridas
RUN apt-get install -y php8.1-fpm php8.1-cli php8.1-common php8.1-mysql \
    php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath \
    php8.1-intl php8.1-soap php8.1-readline php8.1-swoole

# Actualiza PECL e instala extensiones adicionales
RUN pecl channel-update pecl.php.net
RUN pecl install pcov
RUN phpenmod mbstring xml gd zip pcov pcntl sockets bcmath pdo pdo_mysql soap swoole

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copia los binarios de Composer y RoadRunner
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=spiralscout/roadrunner:2.4.2 /usr/bin/rr /usr/bin/rr

# Establece el directorio de trabajo
WORKDIR /app

# Copia los archivos de la aplicación al contenedor
COPY . .

# Copia el archivo .env.example a .env
COPY .env.example .env

# Instala las dependencias del proyecto
RUN composer install --no-scripts --no-autoloader

# Genera el autoloader de Composer optimizado
RUN composer dump-autoload --optimize

# Configura los permisos correctos para los directorios de almacenamiento y caché
RUN chmod -R 775 storage bootstrap/cache

# Genera la clave de la aplicación
RUN php artisan key:generate

# Instala las dependencias adicionales
RUN composer require laravel/octane spiral/roadrunner

# Crea el directorio para los logs
RUN mkdir -p /app/storage/logs

# Instala Laravel Octane con Swoole
RUN php artisan octane:install --server="swoole"

# Comando para iniciar la aplicación
CMD php artisan octane:start --server="swoole" --host="0.0.0.0"

# Expone el puerto 8000 para acceder a la aplicación
EXPOSE 8000