project = pdfraktor
compose = docker-compose -p $(project)
exec = $(compose) exec

ifeq ($(OS),Windows_NT)
	True=powershell "$$true"
else
	True=true
endif

.PHONY:
help:
	@echo Usage: make [target]
	@echo
	@echo Targets:
	@echo   help        Show this help
	@echo   build       Build the project
	@echo   start       Start the project
	@echo   stop        Stop the project
	@echo   restart     Restart the project
	@echo   clean       Remove all containers and volumes
	@echo   exec        Open a shell in the app container
	@echo   logs        Show the logs of the app container
	@echo   watch       Watch the source files and rebuild on change
	@echo   sql         Open a MySQL shell
	@echo   test        Run the tests

.PHONY:
all: build start

.PHONY:
build :
	@$(compose) build

.PHONY:
rebuild: clean build

.PHONY:
rerun: stop rebuild start

.PHONY:
start-container:
	@$(compose) up -d

.PHONY:
start-no-migrate: start-container install

.PHONY:
start: start-container install sf-migrate
	@$(exec) web symfony server:start -d
	@$(True)

.PHONY:
stop:
	@$(exec) web symfony server:stop
	@$(compose) stop
	@$(True)

.PHONY:
restart: stop start

.PHONY:
clean:
	@docker container prune -f
	@docker image rm -f ${project}
	@$(True)

.PHONY:
sf-migrate:
	@$(exec) web symfony console doctrine:database:create --if-not-exists
	@$(exec) web symfony console doctrine:migrations:migrate -n --allow-no-migration

.PHONY:
watch: yarn-install
	@$(exec) web yarn encore dev --watch

.PHONY:
sf-serve:
	@$(exec) web symfony serve

.PHONY:
yarn-watch:
	@$(exec) web yarn encore dev --watch

.PHONY:
yarn-install:
	@$(exec) web yarn

.PHONY:
yarn-dev:
	@$(exec) web yarn encore dev

.PHONY:
composer-install:
	@$(exec) web composer install
	@$(True)

.PHONY:
composer-update:
	@$(exec) web composer update
	@$(True)

.PHONY:
install: composer-install yarn-install yarn-dev

.PHONY:
exec:
	@$(exec) -t -i web bash

.PHONY:
sql:
	@$(exec) -t -i database bash

.PHONY:
logs:
	@$(exec) web symfony server:log

.PHONY:
test:
	@$(exec) web symfony console --env=test doctrine:database:create --if-not-exists
	@$(exec) web symfony console --env=test doctrine:migrations:migrate -n --allow-no-migration
	@$(exec) web ./vendor/bin/phpunit
