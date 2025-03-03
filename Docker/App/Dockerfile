FROM php:8.3-apache

# 必要な拡張モジュールをインストール
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd zip

# php.ini の設定を追加
COPY php.ini /usr/local/etc/php/php.ini

# Apache の設定をコピー
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# mod_rewrite を有効化（Laravelのリダイレクト対応）
RUN a2enmod rewrite
