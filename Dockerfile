# Usa Ubuntu 22.04 como imagen base
FROM ubuntu:22.04

# Evita interacciones durante la instalación de paquetes
ENV DEBIAN_FRONTEND=noninteractive

# Actualiza el sistema e instala paquetes necesarios
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y bash git sudo openssh-client \
    libxml2-dev libonig-dev autoconf gcc g++ make npm \
    libfreetype6-dev libjpeg-turbo8-dev libpng-dev libzip-dev \
    curl unzip nano software-properties-common

# Agrega el repositorio de PHP 8.2 y lo instala junto con las extensiones requeridas
RUN add-apt-repository ppa:ondrej/php -y && \
    apt-get update && \
    apt-get install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath \
    php8.2-intl php8.2-readline php8.2-pcov php8.2-dev

# Instala Swoole desde PECL
RUN pecl install swoole && \
    echo "extension=swoole.so" > /etc/php/8.2/mods-available/swoole.ini && \
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