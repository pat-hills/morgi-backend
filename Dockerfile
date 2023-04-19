FROM php:7.4-apache

# install missing PHP extensions that are not included by default's image.
RUN  apt-get update \
  && apt-get install -y --no-install-recommends \
      libfreetype6-dev \
      libjpeg-dev \
      libpng-dev \
      libzip-dev \
      wget \
      imagemagick \
      libmagickwand-dev \
  && pecl install imagick \
  && docker-php-ext-enable imagick \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install gd zip pdo_mysql -j $(nproc) \
  && apt-get autoremove -y \
  && apt-get clean

# as recommended in the Dockerhub image description page (https://hub.docker.com/_/php).
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# install composer (https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md)
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/be4206c2332e62f91ed568d895975575aa815ddb/web/installer -O - -q | php -- --quiet \
  && mv composer.phar /usr/local/bin/composer

# install some tools to be used when working inside the container
RUN  apt-get update \
  && apt-get install -y --no-install-recommends \
      default-mysql-client-core/stable \
      nano \
      vim-tiny \
  && apt-get autoremove -y \
  && apt-get clean

# deploy the application
WORKDIR /var/www/html/morgi-backend
COPY --chown=www-data . .
RUN composer install --no-progress --no-interaction


# customize Apache - as described in the Dockerhub image description page (https://hub.docker.com/_/php)
ENV APACHE_DOCUMENT_ROOT /var/www/html/morgi-backend/public
RUN \
     sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
  && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# enable apache modules
RUN a2enmod -q rewrite remoteip

RUN echo "RemoteIPHeader X-Forwarded-For" >> /etc/apache2/apache2.conf

# customize php.ini
RUN sed -ri -e 's!^upload_max_filesize\b.*!upload_max_filesize = 80M!' "$PHP_INI_DIR/php.ini"
RUN sed -ri -e 's!^post_max_size\b.*!post_max_size = 80M!' "$PHP_INI_DIR/php.ini"
RUN sed -ri -e 's!^memory_limit\b.*!memory_limit = 1024M!' "$PHP_INI_DIR/php.ini"

# setup some defaults for the application
ENV \
  LOG_CHANNEL=stderr \
  LOG_STDERR_FORMATTER="Monolog\Formatter\JsonFormatter"

CMD [ "apache2-foreground" ]
