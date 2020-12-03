FROM php:7.4-fpm

ENV LANG "C.UTF-8"
ENV LANGUAGE "C.UTF-8"
ENV LC_ALL "C.UTF-8"

ENV PHPIZE_DEPS \
		autoconf \
		dpkg-dev dpkg \
		file \
		g++ \
		gcc \
		libc-dev \
		make \
		pkgconf \
		re2c \
		wget

RUN apt-get update \
    && apt-get install -y \
    $PHPIZE_DEPS \
    git \
    vim \
    bzip2 \
    libbz2-dev \
    unzip \
    libzip-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libghc-postgresql-libpq-dev \
    libicu-dev \
    libxrender1 libfontconfig \
    \
    && docker-php-ext-install bz2 zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/include/postgresql/ \
    && docker-php-ext-install gd pgsql pdo_pgsql bcmath intl \
    && pecl install apcu redis \
    && docker-php-ext-enable apcu redis opcache \
    && apt-get clean \
    && chown www-data:www-data /var/www

RUN COMPOSER=/usr/local/bin/composer set -x \
    && php -r "copy('https://getcomposer.org/download/2.0.7/composer.phar', '$COMPOSER');" \
    && php -r "if (hash_file('SHA256', '$COMPOSER') === '0a060e8caf1d5cde55c4562fd68369b08cf231b8fd30da9c8f994b111557ebe9') { echo 'Installer verified!', PHP_EOL; } else { echo 'composer checksum mismatch!', PHP_EOL; exit(-1); }" \
    && chmod +x $COMPOSER \
    && ln -s $COMPOSER $COMPOSER.phar

ENV PHP_IDE_CONFIG "serverName=simpleTest"
ENV PATH /usr/local/bin/:$PATH

COPY fpm-pool.ini /usr/local/etc/php-fpm.d/www.conf
COPY prod.ini /usr/local/etc/php/conf.d/prod.ini
COPY zzz.docker.conf /usr/local/etc/php-fpm.d/zzz.docker.conf


# fix files permissions
RUN groupmod -o -g 1000 www-data && usermod -o -u 1000 www-data

USER www-data

WORKDIR /var/www/simple_test
