FROM alpine:3.16
LABEL Maintainer="Tim de Pater <code@trafex.nl>" \
      Description="Lightweight container with Nginx 1.18 & PHP-FPM 8 based on Alpine Linux."

# Install packages and remove default server definition
RUN apk --no-cache add php php8-fpm php8-opcache php8-mysqli php8-json php8-openssl php8-curl \
    php8-zlib php8-xml php8-phar php8-intl php8-dom php8-simplexml php8-xmlreader php8-xmlwriter php8-ctype php8-session php8-tokenizer \
    php8-mbstring php8-gd php8-iconv nginx supervisor curl nodejs npm composer && \
    rm /etc/nginx/http.d/default.conf

RUN npm install -g yarn

# Configure nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY docker/fpm-pool.conf /etc/php8/php-fpm.d/www.conf
COPY docker/php.ini /etc/php8/conf.d/custom.ini

# Configure supervisord
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Setup document root
RUN mkdir -p /var/www/html

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R nobody.nobody /var/www/html && \
  chown -R nobody.nobody /run && \
  chown -R nobody.nobody /var/lib/nginx && \
  chown -R nobody.nobody /var/log/nginx

# Switch to use a non-root user from here on
USER nobody

# Add application
WORKDIR /var/www/html
COPY --chown=nobody ./ /var/www/html/

# Expose the port nginx is reachable on
EXPOSE 8080

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping
