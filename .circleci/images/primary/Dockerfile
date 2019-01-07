FROM notnoopci/php:5.6-browsers

RUN sudo apt-get install libmcrypt-dev libreadline-dev
RUN sudo docker-php-ext-install pdo_mysql mcrypt
RUN sudo pecl install xdebug && sudo docker-php-ext-enable xdebug

RUN sudo curl -O https://bootstrap.pypa.io/get-pip.py
RUN sudo python get-pip.py
RUN sudo pip install awscli --upgrade
RUN sudo wget https://github.com/jwilder/dockerize/releases/download/v0.5.0/dockerize-alpine-linux-amd64-v0.5.0.tar.gz && sudo tar -C /usr/local/bin -xzvf dockerize-alpine-linux-amd64-v0.5.0.tar.gz && sudo rm dockerize-alpine-linux-amd64-v0.5.0.tar.gz
