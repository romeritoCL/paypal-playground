FROM alpine:3.20

# Install packages and remove default server definition
RUN apk --no-cache --no-check-certificate add php php83-fpm php83-opcache php83-mysqli php83-json php83-openssl php83-curl \
    php83-zlib php83-xml php83-phar php83-intl php83-dom php83-simplexml php83-xmlreader php83-xmlwriter php83-ctype php83-session php83-tokenizer \
    php83-mbstring php83-gd php83-iconv nginx supervisor curl composer openssl nodejs npm && \
    rm /etc/nginx/http.d/default.conf

# Self Signed Certificate ?? Uncomment this lines
#COPY docker/cert/cert.crt /usr/local/share/ca-certificates/cert.crt
# NPM registry issues? use local yarn, please comment this lines if you have issues with npm registry.
RUN npm install -g yarn

# Configure nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY docker/fpm-pool.conf /etc/php83/php-fpm.d/www.conf
COPY docker/php.ini /etc/php83/conf.d/custom.ini

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
