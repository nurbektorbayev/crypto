# Используем официальный образ PHP 8.3 с необходимыми расширениями
FROM php:8.3-fpm

# Устанавливаем зависимости для Laravel, Horizon и PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    supervisor \
    git \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql zip pcntl

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Аргумент для проекта
ARG PROJECT_NAME

# Устанавливаем зависимости Laravel
WORKDIR /var/www

# Копируем исходный код Laravel для конкретного проекта
COPY ../$PROJECT_NAME/source /var/www

# Копируем конфигурацию Supervisor для проекта
COPY ../$PROJECT_NAME/docker/app/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN composer install --no-dev --optimize-autoloader

# Открываем порты для PHP-FPM
EXPOSE 9000

# Настроим команду для запуска Supervisor, который будет запускать Horizon и PHP-FPM
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
