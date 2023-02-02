#bin/bash

yarn install
yarn encore prod
composer install
bin/phpunit
docker run --rm -e APP_ENV=prod --volume $PWD:/app composer install  --no-dev --prefer-source --no-interaction
docker build . -t romeritocl/paypal-playground:latest
docker tag paypal-playground_paypal-playground romeritocl/paypal-playground:latest
docker push romeritocl/paypal-playground
composer install
yarn encore dev
