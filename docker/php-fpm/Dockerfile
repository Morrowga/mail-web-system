FROM php:8.2-fpm

RUN apt-get update && \
  apt-get -y install git unzip libzip-dev libicu-dev libonig-dev \
  zlib1g-dev \ 
  libjpeg-dev \
  vim \
  libpng-dev \
  libfreetype6-dev \
  libjpeg62-turbo-dev && \
  apt-get clean && \
  rm -rf /var/lib/apt/lists/*

RUN apt update
RUN apt install -y imagemagick libmagickwand-dev
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# PHP拡張ライブラリのインストール
RUN docker-php-ext-install pdo_mysql zip bcmath opcache -j$(nproc) gd  && \
docker-php-ext-configure gd --with-freetype --with-jpeg

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

ADD docker/php-fpm/php.ini /usr/local/etc/php/

RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y  nodejs
RUN npm install -g yarn

WORKDIR /var/www/bloom-mail

EXPOSE 5173
