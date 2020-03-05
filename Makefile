project = pdfraktor
compose = docker-compose -p $(project)
exec = $(compose) exec

all: build start

build :
	@$(compose) build

rebuild: clean build

rerun: stop rebuild start

start-container:
	@$(compose) up -d

start-no-migrate: start-container install

start: start-container install sf-migrate

stop:
	@$(compose) stop
	@true

restart: stop start

clean:
	@docker container prune -f
	@docker image rm -f ${project}
	@true

sf-migrate:
	@$(exec) web symfony console doctrine:database:create --if-not-exists
	@$(exec) web symfony console doctrine:migrations:migrate -n --allow-no-migration

sf-serve:
	@$(exec) web symfony serve

yarn-watch:
	@$(exec) web yarn encore dev --watch

yarn-install:
	@$(exec) web yarn

yarn-dev:
	@$(exec) web yarn encore dev

composer-install:
	@$(exec) web composer install
	@true

install: composer-install yarn-install yarn-dev

exec:
	@$(exec) web bash

sql:
	@$(exec) database bash

