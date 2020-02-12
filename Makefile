project = pdfraktor

all: build start

build :
	@docker-compose -p ${project} build

rebuild: clean build

rerun: stop rebuild start install

start:
	@docker-compose -p ${project} up -d
	make install
	make sf-migrate || true
	make sf-serve

stop:
	@docker-compose -p ${project} stop || true

restart: stop start

clean:
	@docker container prune -f
	@docker image rm -f ${project} || true

sf-migrate:
	@docker-compose exec web symfony console make:migration
	@docker-compose exec web symfony console doctrine:migrations:migrate

sf-serve:
	@docker-compose exec web symfony serve

yarn-watch:
	@docker-compose exec web yarn encore dev --watch

install:
	@docker-compose exec web composer install || true
	@docker-compose exec web yarn

exec:
	@docker-compose exec web bash

