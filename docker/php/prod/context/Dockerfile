FROM php:8.2.7-fpm-alpine

RUN apk add --no-cache libzip \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
        icu-dev \
        icu-libs \
        freetype-dev \
        postgresql-dev \
        nodejs \
        npm \
        linux-headers && \
    # Install build dependencies
    apk add --no-cache --virtual .docker-php-global-dependancies \
        oniguruma-dev \
        libzip-dev \
        autoconf \
        g++ \
        make \
        wget && \
    # Install php extensions
    php -m && \
    docker-php-ext-install bcmath && \
    docker-php-ext-install pcntl && \
    docker-php-ext-install intl && \
    docker-php-ext-install zip && \
    docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype && \
    docker-php-ext-install gd && \
    # Install MySQL
    docker-php-ext-install mysqli && \
    docker-php-ext-install pdo pdo_pgsql && \
    # Install opcache \
    docker-php-ext-install opcache && \
    docker-php-ext-enable opcache && \
    # Install Redis
    pecl install redis && \
    docker-php-ext-enable redis && \
    php -m && \
    # Clear cache
    rm -rf /var/cache/apk/* && \
        docker-php-source delete

# Install composer.
RUN EXPECTED_SIGNATURE=$(wget -q -O - https://composer.github.io/installer.sig) && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', 'composer-setup.php');") && \
    if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then \
      >&2 echo 'ERROR: Invalid installer signature' && \
      rm composer-setup.php && \
      exit 1; \
    else \
      php composer-setup.php --install-dir=/usr/bin --filename=composer && \
      RESULT=$? && \
      rm composer-setup.php && \
      exit $RESULT && \
      composer -V; \
    fi



RUN mkdir /npm_cache
RUN mkdir /.cache
RUN chown -R 1002:1002 "/npm_cache"
RUN chown -R 1002:1002 /.cache
RUN chmod -R 777  /npm_cache
RUN chmod -R 777  /.cache


RUN apk add bash

#RUN apk add --no-cache supervisor
#COPY docker/php/prod/context/supervisord.conf /etc/supervisor.d/supervisord.conf

WORKDIR /app