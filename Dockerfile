FROM php:8.3-fpm
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    mc \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
COPY app /var/www/symfony
WORKDIR /var/www/symfony
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN chown -R www-data:www-data var
EXPOSE 9000
CMD ["php-fpm"]