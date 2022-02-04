FROM php:7.4-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
	libpq-dev \
    zip \
    unzip \
    libmcrypt-dev \
    libcurl4-openssl-dev \
    liblua5.3-dev \
    lua5.3 \
    && cp /usr/include/lua5.3/lua.h /usr/include \
    && ln -s /usr/include/lua*.*/ /usr/include/lua \
    && cp /usr/lib/x86_64-linux-gnu/liblua5.3.a /usr/lib/liblua.a \
    && cp /usr/lib/x86_64-linux-gnu/liblua5.3.so /usr/lib/liblua.so \
    && pecl install mcrypt-1.0.3 \
    && pecl install lua-2.0.7 \
    && docker-php-ext-enable mcrypt lua

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
        bcmath \
        curl \
        exif \
        gd \
        iconv \
        mbstring \
        pdo \
        mysqli \
        pdo_mysql \
        pcntl \
        tokenizer \
        xml \
        zip \
        intl

# Enable mod rewrite
# RUN a2enmod rewrite headers

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Change PHP Memory Limit
RUN cd /usr/local/etc/php/conf.d/ && \
  echo 'memory_limit = 256M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini && \
  echo 'post_max_size = 50M' >> /usr/local/etc/php/conf.d/docker-php-maxsize.ini && \
  echo 'upload_max_filesize = 40M' >> /usr/local/etc/php/conf.d/docker-php-maxsize.ini && \
  echo 'max_execution_time = 120' >> /usr/local/etc/php/conf.d/docker-php-maxexec.ini

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user

RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

RUN chmod 775 -R /var/www && \
    chgrp www-data -R /var/www

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

# Set working directory
WORKDIR /var/www

USER $user