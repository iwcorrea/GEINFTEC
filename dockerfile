FROM php:8.2-apache

# Instalar dependencias del sistema para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    && docker-php-ext-enable pdo_pgsql

# Habilitar mod_rewrite (opcional, pero útil)
RUN a2enmod rewrite

# Copiar archivos del proyecto
COPY . /var/www/html/

# Crear carpeta uploads con permisos
RUN mkdir -p /var/www/html/uploads && chmod 755 /var/www/html/uploads

# Exponer puerto 80
EXPOSE 80