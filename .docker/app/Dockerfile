### Base container
FROM debian:10-slim

# Workaround for readline error
ENV DEBIAN_FRONTEND noninteractive

### Install system packages
RUN apt-get update -qq && apt-get install -yqq \
    wget \
    curl \
    acl \
    zip \
    unzip \
    procps \
    vim \
    telnet \
    iputils-ping \
    apt-transport-https \
    lsb-release \
    ca-certificates \
    --no-install-recommends

### Add php7.4 repo
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list

### Install app packages
RUN apt-get update -qq && apt-get install -qq -y \
    nginx \
    supervisor \
    composer \
    php7.4-cli \
    php7.4-fpm \
    php7.4-common \
    php7.4-curl \
    php7.4-gd \
    php7.4-json \
    php7.4-mbstring \
    php7.4-opcache \
    php7.4-xml \
    php7.4-apcu \
    php7.4-intl \
    php7.4-bcmath \
    php7.4-pdo \
    php7.4-mysql \
    php7.4-pgsql \
    php7.4-tokenizer \
    php7.4-redis \
    php7.4-mongodb \
    php7.4-amqp \
    php7.4-zip \
    --no-install-recommends
### Clear apt cache
RUN rm -r /var/lib/apt/lists/*

### Configure php
RUN mkdir -m 0775 -p /var/run/php && chown -R www-data:www-data /var/run/php
RUN mkdir -p /var/log/php && touch /var/log/php/cli.log

# Prepare dirs
RUN mkdir -m 755 -p /var/www/app && chown -R www-data:www-data /var/www/app
RUN mkdir -m 0775 -p /var/run/php && chown -R www-data:www-data /var/run/php
RUN mkdir -p /var/tmp/nginx
### Configure php
COPY ./php/cli/php-dev.ini /etc/php/7.4/cli/conf.d/99-override.ini
COPY ./php/fpm/php-dev.ini /etc/php/7.4/fpm/conf.d/99-override.ini
COPY ./php/app.pool.conf /etc/php/7.4/fpm/pool.d/www.conf
### Configure nginx
# Copy configs
COPY ./nginx/nginx.conf /etc/nginx/
RUN rm -rf /etc/nginx/sites-available/default
COPY ./nginx/app.conf /etc/nginx/conf.d/app.conf

### Configure supervisord
COPY ./supervisor/supervisor.conf /etc/supervisor/supervisord.conf
COPY ./supervisor/app.conf /etc/supervisor/conf.d/webserver.conf

RUN service php7.4-fpm stop && service nginx stop

# Prepare app
RUN mkdir -m 755 -p /var/www/app
RUN chown -R www-data:www-data /var/www/app

# Install composer plugin
RUN composer global require hirak/prestissimo -q

WORKDIR /var/www/app
EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
