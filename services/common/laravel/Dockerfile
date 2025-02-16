# Используем официальный образ PHP 8.3 с необходимыми расширениями
FROM php:8.3-fpm

# Устанавливаем зависимости для Laravel, Horizon, PostgreSQL и Redis
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    supervisor \
    git \
    libpq-dev \
    libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql zip pcntl sockets

# Устанавливаем PHP-расширение Redis
RUN pecl install redis && docker-php-ext-enable redis

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Аргумент для проекта
ARG PROJECT_NAME

# Устанавливаем зависимости Laravel
WORKDIR /var/www

# Копируем исходный код Laravel
COPY ../$PROJECT_NAME/source /var/www

# Копируем конфигурацию Supervisor
COPY ../$PROJECT_NAME/docker/app/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Устанавливаем права на директории
RUN chown -R www-data:www-data /var/www

# Запускаем контейнер: устанавливаем зависимости перед Supervisor
CMD sh -c "composer install --no-dev --optimize-autoloader && supervisord -c /etc/supervisor/conf.d/supervisord.conf"
