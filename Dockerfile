# Usa Ubuntu 22.04 como imagen base
FROM ubuntu:22.04

# Evita interacciones durante la instalación de paquetes
ENV DEBIAN_FRONTEND=noninteractive

# Actualiza el sistema e instala paquetes necesarios
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y bash git sudo openssh-client \
    libxml2-dev libonig-dev autoconf gcc g++ make npm \
    libfreetype6-dev libjpeg-turbo8-dev libpng-dev libzip-dev \
    curl php-cli php-mbstring php-xml php-zip unzip \
    nano software-properties-common

# Agrega el repositorio de PHP 8.1 y lo instala junto con las extensiones requeridas
RUN add-apt-repository ppa:ondrej/php -y && \
    apt-get update && \
    apt-get install -y php8.1-fpm php8.1-cli php8.1-common php8.1-mysql \
    php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath \
    php8.1-intl php8.1-readline php8.1-pcov

# Instala Swoole desde PECL
RUN apt-get install -y php8.1-dev && \
    pecl install swoole && \
    echo "extension=swoole.so" > /etc/php/8.1/mods-available/swoole.ini && \
    phpenmod swoole

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establece el directorio de trabajo
WORKDIR /app

# Copia los archivos de la aplicación al contenedor
COPY . .

# Copia el archivo .env.example a .env si no existe
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Instala las dependencias del proyecto
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Configura los permisos correctos para los directorios de almacenamiento y caché
RUN chmod -R 775 storage bootstrap/cache

# Genera la clave de la aplicación si no existe
RUN php artisan key:generate --force

# Instala Laravel Octane
RUN composer require laravel/octane spiral/roadrunner --no-interaction

# Instala Octane con Swoole
RUN php artisan octane:install --server=swoole

# Optimiza la configuración para producción
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Comando para iniciar la aplicación con Octane y Swoole
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]

# Expone el puerto 8000 para acceder a la aplicación
EXPOSE 8000