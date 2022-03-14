FROM php:7.4-apache
ARG USERID
ARG GROUPID

#RUN groupadd --non-unique --gid $GROUPID usergroup && useradd -l -d /home/user -m -s /bin/bash --non-unique --gid $GROUPID --uid $USERID -p $(date +%s | sha256sum | base64 | head -c 32 ; echo) user && chown user:usergroup /var/www

RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install libonig-dev

# Install dependency free extensions
RUN docker-php-ext-install pdo_mysql mbstring

# Installing ZIP
RUN apt-get install -y zlib1g-dev libzip-dev
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

# Installing ICU
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

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

## xdebug 3
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

# Enable mod_rewrite
RUN a2enmod rewrite

# Allow access to root dir
RUN chown -R www-data:www-data /var/www/html

# Copy apache vhost file to proxy php requests to php-fpm container
#COPY apache.conf /usr/local/apache2/conf/apache.conf
#RUN echo "Include /usr/local/apache2/conf/apache.conf" >> /usr/local/apache2/conf/httpd.conf

# Install nodejs and gulp
RUN apt-get install curl
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash
RUN apt-get install nodejs -y
RUN npm install -g gulp-cli
