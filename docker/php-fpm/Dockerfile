FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && \
  apt-get -y install git unzip libzip-dev libicu-dev libonig-dev \
  zlib1g-dev \
  libjpeg-dev \
  vim \
  libpng-dev \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libc-client-dev  # Required for IMAP extension

# Install ImageMagick and Imagick
RUN apt-get install -y imagemagick libmagickwand-dev
RUN pecl install imagick && docker-php-ext-enable imagick

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip bcmath opcache gd && \
  docker-php-ext-configure gd --with-freetype --with-jpeg

# Install IMAP extension
RUN apt-get install -y libssl-dev libc-client-dev \
  && docker-php-ext-install imap

# Install PCNTL extension (if needed)
RUN docker-php-ext-install pcntl
RUN docker-php-ext-configure pcntl --enable-pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

# Install Node.js and Yarn
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs
RUN npm install -g yarn

# Set the working directory
WORKDIR /var/www/bloom-mail

# Expose the app's port
EXPOSE 5173
