FROM php:8.2-apache

# Instalar dependencias del sistema y extensiones de PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql && \
    docker-php-ext-enable pdo_pgsql

# Habilitar mod_rewrite (opcional)
RUN a2enmod rewrite

# Copiar todos los archivos al directorio de Apache
COPY . /var/www/html/

# Crear directorio uploads con permisos
RUN mkdir -p /var/www/html/uploads && chmod 755 /var/www/html/uploads

# Exponer el puerto 80
EXPOSE 80