# PayPal PlayGround <img src="https://developer.paypal.com/components/dx/img/logo-PayPal-Developer.svg" width="100" align="right">

[![Build Status](https://travis-ci.com/romeritoCL/paypal-playground.svg?branch=master)](https://travis-ci.com/romeritoCL/paypal-playground)
[![Latest Stable Version](https://poser.pugx.org/romeritoCL/paypal-playground/v/stable)](https://packagist.org/packages/romeritoCL/paypal-playground)
[![composer.lock](https://poser.pugx.org/romeritoCL/paypal-playground/composerlock)](https://packagist.org/packages/romeritoCL/paypal-playground)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/romeritoCL/paypal-playground/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/romeritoCL/paypal-playground/?branch=master)
[![License](https://poser.pugx.org/romeritoCL/paypal-playground/license)](//packagist.org/packages/romeritoCL/paypal-playground)

## :hand: What am I?
Symfony 5 Project to show how to implement and integrate the PayPal's APIs. Including Braintree, V2 Orders, Payments, Billing, Connect with PayPal etc...

## :arrow_forward: Is it live? Demo
See our [https://paypal.devoralive.com](https://paypal.devoralive.com) demo site.

## :floppy_disk: How we get working?

1. Git clone the project:
```bash
git clone https://github.com/romeritoCL/paypal-playground.git
```

2. Start Docker containers:
```bash
docker-compose up -d
```

3. Update dependencies:
```bash
docker-compose exec paypal-playground composer install
docker-compose exec paypal-playground yarn install
```

4. Go to site:
[http://localhost:8086](http://localhost:8086)

## :gear: Continuous Deployment
This project is configured with continuous deployment. Any PR merged to master branch will generate a build on [Travis CI PayPal-Playground](https://travis-ci.org/github/romeritoCL/paypal-playground). The CI software will run the tests and push the docker build to [DockerHub romeritocl/paypal-playground](https://hub.docker.com/repository/docker/romeritocl/paypal-playground). Once the tag latest is updated, the server will detect a new image and will download it and recreate the container.
