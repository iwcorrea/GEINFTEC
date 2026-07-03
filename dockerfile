FROM php:8.2-apache

# Instalar dependencias del sistema y extensiones para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo_pgsql \
    && docker-php-ext-enable pgsql pdo_pgsql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar archivos del proyecto
COPY . /var/www/html/

# Crear carpeta uploads con permisos
RUN mkdir -p /var/www/html/uploads && chmod 755 /var/www/html/uploads

# Establecer el directorio de trabajo
WORKDIR /var/www/html

EXPOSE 80