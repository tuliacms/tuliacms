FROM php:8.1-apache
ARG USER_ID
ARG GROUP_ID

RUN addgroup --gid $GROUP_ID user
RUN adduser --disabled-password --gecos '' --uid $USER_ID --gid $GROUP_ID user

RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install libonig-dev

# Install dependency free extensions
RUN docker-php-ext-install pdo_mysql mbstring

# Installing ZIP
RUN apt-get install -y zlib1g-dev libzip-dev
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

# Installing INTL
RUN apt-get install -y zlib1g-dev libicu-dev g++
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

# Installing XML
RUN apt-get install -y libxml2-dev
RUN docker-php-ext-install xml

# Installing Imagick
RUN apt-get install libmagickwand-dev --no-install-recommends -y
RUN pecl install imagick
RUN docker-php-ext-enable imagick


## xdebug 3
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

# Enable mod_rewrite
RUN a2enmod rewrite

# Allow access to root dir
RUN chown -R www-data:www-data /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
