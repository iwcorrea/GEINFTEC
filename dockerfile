FROM php:8.2-apache

# Instalar extensiones para PostgreSQL (PDO)
RUN apt-get update && apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql && \
    docker-php-ext-enable pdo_pgsql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar todos los archivos
COPY . /var/www/html/

# Crear directorio uploads con permisos
RUN mkdir -p /var/www/html/uploads && chmod 755 /var/www/html/uploads

# Configurar DocumentRoot
WORKDIR /var/www/html

EXPOSE 80