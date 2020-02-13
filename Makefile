project = pdfraktor

all: build start

build :
	@docker-compose -p ${project} build

rebuild: clean build

rerun: stop rebuild start

start-container:
	@docker-compose -p ${project} up -d

start-no-migrate: start-container install

start: start-container install sf-migrate

stop:
	@docker-compose -p ${project} stop || true

restart: stop start

clean:
	@docker container prune -f
	@docker image rm -f ${project} || true

sf-migrate:
	@docker-compose exec web symfony console doctrine:database:create --if-not-exists
	@docker-compose exec web symfony console doctrine:migrations:migrate -n --allow-no-migration

sf-serve:
	@docker-compose exec web symfony serve

yarn-watch:
	@docker-compose exec web yarn encore dev --watch

yarn-install:
	@docker-compose exec web yarn

yarn-dev:
	@docker-compose exec web yarn encore dev

composer-install:
	@docker-compose exec web composer install || true

install: composer-install yarn-install yarn-dev

exec:
	@docker-compose exec web bash

exec-db:
	@docker-compose exec database bash
