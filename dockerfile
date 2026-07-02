FROM php:8.2-apache

# Habilitar mod_rewrite para URLs amigables (opcional)
RUN a2enmod rewrite

# Instalar extensiones de PHP necesarias (mysqli ya está, pero podemos asegurarlo)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Crear carpeta uploads y dar permisos de escritura
RUN mkdir -p /var/www/html/uploads && chown -R www-data:www-data /var/www/html/uploads

# Copiar todos los archivos del proyecto al contenedor
COPY . /var/www/html/

# Exponer el puerto 80
EXPOSE 80