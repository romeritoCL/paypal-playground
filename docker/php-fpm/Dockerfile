FROM phpdockerio/php72-fpm:latest
WORKDIR "/var/www"
ENV PUID=1001

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install  php-memcached php7.2-mysql php7.2-gd \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY docker/php-fpm/php-ini-overrides.ini /etc/php/7.2/fpm/conf.d/99-overrides.ini
RUN groupmod -g $PUID www-data && usermod -u $PUID www-data
CMD [ "/usr/bin/php-fpm" ]
