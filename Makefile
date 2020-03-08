project = pdfraktor
compose = docker-compose -p $(project)
exec = $(compose) exec

all: build start

build :
	@$(compose) build

rebuild: clean build

rerun: stop rebuild start

start-container:
<<<<<<< Updated upstream
	@$(compose) up -d
=======
	@docker-compose -p ${project} up -d
	@docker-compose -p ${project} exec web mkdir -p /var/www/project/public/uploads/pdf
	@docker-compose -p ${project} exec web chmod 777 -R /var/www/project/public/uploads/pdf
<<<<<<< Updated upstream
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes

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
<<<<<<< Updated upstream
<<<<<<< Updated upstream
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
=======
	@docker-compose -p ${project} exec web symfony console doctrine:database:create --if-not-exists
	@docker-compose -p ${project} exec web symfony console doctrine:migrations:migrate -n --allow-no-migration

sf-serve:
	@docker-compose -p ${project} exec web symfony serve

yarn-watch:
	@docker-compose -p ${project} exec web yarn encore dev --watch

yarn-install:
	@docker-compose -p ${project} exec web yarn

yarn-dev:
	@docker-compose -p ${project} exec web yarn encore dev

composer-install:
	@docker-compose -p ${project} exec web composer install || true
>>>>>>> Stashed changes
=======
	@docker-compose -p ${project} exec web symfony console doctrine:database:create --if-not-exists
	@docker-compose -p ${project} exec web symfony console doctrine:migrations:migrate -n --allow-no-migration

sf-serve:
	@docker-compose -p ${project} exec web symfony serve

yarn-watch:
	@docker-compose -p ${project} exec web yarn encore dev --watch

yarn-install:
	@docker-compose -p ${project} exec web yarn

yarn-dev:
	@docker-compose -p ${project} exec web yarn encore dev

composer-install:
	@docker-compose -p ${project} exec web composer install || true
>>>>>>> Stashed changes

install: composer-install yarn-install yarn-dev

exec:
<<<<<<< Updated upstream
<<<<<<< Updated upstream
	@$(exec) web bash

sql:
	@$(exec) database bash

=======
=======
>>>>>>> Stashed changes
	@docker-compose -p ${project} exec web bash

exec-db:
	@docker-compose -p ${project} exec database bash

test:
	@docker-compose -p ${project} exec web symfony php ./bin/phpunit
<<<<<<< Updated upstream
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
