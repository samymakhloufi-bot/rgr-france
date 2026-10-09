FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        mysqli \
        pdo_mysql \
    && a2enmod rewrite headers expires \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY docker/apache/rgr.conf /etc/apache2/conf-available/rgr.conf

RUN a2enconf rgr

WORKDIR /var/www/html

