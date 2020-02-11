project = pdfraktor

all: build start

build :
	@docker-compose -p ${project} build

rebuild: clean build

rerun: stop rebuild start

start:
	@docker-compose -p ${project} up -d

stop:
	@docker-compose -p ${project} stop || true

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

