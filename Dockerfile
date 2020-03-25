FROM php:7.3.9-alpine

WORKDIR /opt/php-friendly

CMD ["./vendor/bin/phpunit", "--testsuite", "all"]