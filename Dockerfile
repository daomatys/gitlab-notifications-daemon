FROM library/php:8.1-cli

WORKDIR /opt/gitlab-notifications-daemon

RUN apt update -yyq && apt install -yyq zip libzip-dev
RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install sockets

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin/ --filename composer; rm composer-setup.php

COPY ./ .
RUN composer install

ARG TELEGRAM_BOT_TOKEN
ARG TELEGRAM_USER_CHAT_ID
ARG GITLAB_TOKEN
ENV TELEGRAM_BOT_TOKEN $TELEGRAM_BOT_TOKEN
ENV TELEGRAM_USER_CHAT_ID $TELEGRAM_USER_CHAT_ID
ENV GITLAB_TOKEN $GITLAB_TOKEN

CMD php ./index.php
