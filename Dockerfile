# Usa una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Copia todo el contenido de tu proyecto al directorio raíz del servidor web
COPY . /var/www/html/

# Habilita mod_rewrite 
RUN a2enmod rewrite

# Render usa el puerto 10000, así que lo configuramos
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Expone el puerto correcto
EXPOSE 10000
