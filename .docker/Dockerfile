FROM php:apache

# Quelques library necessaires
RUN apt -y update && \
    apt -y install --no-install-recommends locales \
    apt-utils zbar-tools bash wget nodejs git libicu-dev ghostscript dbus gnupg2

# yarn configuration
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt -y update && apt -y install --no-install-recommends yarn

# les locales, toujours utiles
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# Installer PDO
RUN docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# Installation de composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
    mv composer.phar /usr/local/bin/composer

# Installation de symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN export PATH="$HOME/.symfony/bin:$PATH"
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# Dossier du site
RUN mkdir /var/www/project

RUN service apache2 restart

EXPOSE 80
EXPOSE 8000

WORKDIR /var/www/project

CMD apache2-foreground
