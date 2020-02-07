all: build migration

build:
	composer install
	yarn

migration:
	php bin/console make:migration
	php bin/console doctrine:migrations:migrate

serve:
	symfony console serve
