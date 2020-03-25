#!/bin/bash
docker run -ti -v $(pwd):/opt/php-friendly php:7.3.9-alpine /opt/php-friendly/scripts/installDeps.sh
