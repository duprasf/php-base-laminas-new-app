FROM jack.hc-sc.gc.ca/php/php-base-laminas:latest
#FROM php-base-laminas:latest

LABEL title = 'New application based on the Laminas framework'
LABEL author = 'Web/Mobile Team (imsd.web-dsgi@hc-sc.gc.ca)'
LABEL source = 'https://github.hc-sc.gc.ca/hs/php-base-laminas-new-app'

# update the OS and install common modules
RUN apt-get update -y && \
    apt-get upgrade -y nodejs npm && \
    rm -rf /var/lib/apt/lists/*

COPY ExampleModule/ /var/www/apps/ExampleModule

WORKDIR /var/www
