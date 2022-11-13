FROM php:7.4-apache

# Quelques library necessaires
RUN mkdir -p /usr/share/man/man1mkdir -p /usr/share/man/man1
RUN apt -y update
RUN apt -y install --no-install-recommends \
    locales \
    apt-utils \
    zbar-tools \
    bash \
    wget \
    nodejs \
    git \
    dbus \
    libicu-dev \
    ghostscript \
    gnupg2 \
    pdftk \
    zlib1g-dev \
    libzip-dev \
    unzip

# yarn configuration
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt -y update && apt -y install --no-install-recommends yarn

# les locales, toujours utiles
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# Installer les extensions PHP
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-install zip

# Installer composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
    mv composer.phar /usr/local/bin/composer

# Installer symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli

# Activer de dbus
RUN dbus-uuidgen > /var/lib/dbus/machine-id
RUN mkdir -p /var/run/dbus
RUN dbus-daemon --config-file=/usr/share/dbus-1/system.conf --print-address

# Activer rewrite mode htaccess
RUN a2enmod rewrite
RUN service apache2 restart

EXPOSE 80
EXPOSE 8000

# Dossier du site
RUN mkdir /var/www/project

WORKDIR /var/www/project

RUN sed -i '/disable ghostscript format types/,+6d' /etc/ImageMagick-6/policy.xml

CMD apache2-foreground
