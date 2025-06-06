FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Ajusta DocumentRoot para /var/www/html/public/frontend
RUN sed -i 's|/var/www/html|/var/www/html/public/frontend|g' /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

RUN echo "Alias /frontend /var/www/html/frontend\n\
<Directory /var/www/html/frontend>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride None\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/frontend.conf \
    && a2enconf frontend

RUN echo "<Directory /var/www/html/public/frontend>\n\
    AllowOverride All\n\
</Directory>" >> /etc/apache2/apache2.conf

EXPOSE 80

RUN composer install

CMD ["apache2-foreground"]
