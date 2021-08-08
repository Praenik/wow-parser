FROM php:7.2-fpm
RUN apt-get update \
       && apt-get install -y zlib1g-dev libicu-dev g++ libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev \
       && docker-php-ext-install mbstring \
       && docker-php-ext-install intl \
       && docker-php-ext-install json \
       && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
       && docker-php-ext-install gd \
       && docker-php-ext-install zip \
       && docker-php-ext-install mysqli pdo pdo_mysql \
       && curl -sSL https://getcomposer.org/installer | php \
       && mv composer.phar /usr/local/bin/composer
