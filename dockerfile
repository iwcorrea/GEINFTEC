FROM php:8.2-apache

# Instalar extensiones necesarias (mysqli)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Habilitar mod_rewrite para URLs limpias (opcional)
RUN a2enmod rewrite

# Copiar todo el código al directorio de Apache
COPY . /var/www/html/

# Cambiar propietario para que Apache pueda escribir en uploads
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Exponer puerto 80
EXPOSE 80