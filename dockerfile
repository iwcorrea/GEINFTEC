FROM php:8.2-apache

# Instalar extensiones para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite (opcional)
RUN a2enmod rewrite

# Copiar todos los archivos al directorio de Apache
COPY . /var/www/html/

# Establecer permisos para la carpeta uploads (si existe)
RUN chown -R www-data:www-data /var/www/html/uploads 2>/dev/null || true

# Exponer puerto 80
EXPOSE 80