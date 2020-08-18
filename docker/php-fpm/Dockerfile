FROM phpdockerio/php73-fpm:latest
WORKDIR "/var/www"
ENV PUID=1001

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install  php-memcached php7.3-mysql php7.3-gd \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY docker/php-fpm/php-ini-overrides.ini /etc/php/7.3/fpm/conf.d/99-overrides.ini
RUN groupmod -g $PUID www-data && usermod -u $PUID www-data
