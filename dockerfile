FROM jack.hc-sc.gc.ca/php/php-base-laminas:latest

LABEL title="New application based on the Laminas framework"
LABEL author='Web/Mobile Team (imsd.web-dsgi@hc-sc.gc.ca)'
LABEL source='https://github.hc-sc.gc.ca/hs/php-base-laminas-new-app'

# update the OS and install common modules
RUN apt-get update -y \
    && apt-get upgrade -y nodejs npm

# If your app required SSH connection to other server, copy these 3 line in your own dockerfile
#RUN apt-get update && apt-get install -y libssh2-1-dev libssh2-1 \
#    && pecl install ssh2-1.4.1 \
#    && docker-php-ext-enable ssh2

COPY ExampleModule/ /var/www/apps/ExampleModule

WORKDIR /var/www

# Clean up the image
RUN rm -rf /var/lib/apt/lists/*
