FROM php:8.4

RUN apt-get update -y && apt-get install -y \
    libzip-dev \
    openssl \
    zip \
    unzip \
    git \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libmagickwand-dev \
    libmagickcore-dev \
    imagemagick \
    && apt-get clean
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        pdo \
        pdo_pgsql \
        bcmath \
        gd \
        zip

RUN pecl install imagick && docker-php-ext-enable imagick

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY . /app

RUN git config --global --add safe.directory /app

RUN composer install

CMD php artisan serve --host=0.0.0.0 --port=8000
EXPOSE 8000