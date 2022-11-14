project = pdfraktor
compose = docker-compose -p $(project)
exec = $(compose) exec

ifeq ($(OS),Windows_NT)
	True=powershell "$$true"
else
	True=true
endif

.PHONY:
all: build start

.PHONY:
help:
	@echo Usage: make [target]
	@echo Targets:
	@echo   help              Show this help
	@echo   build             Build the project
	@echo   rebuild           CLean container and rebuild docker images
	@echo   start             Start the project
	@echo   stop              Stop the project
	@echo   restart           Restart the project
	@echo   clean             Remove all containers and volumes
	@echo   exec              Open a shell in the app container
	@echo   logs              Show the logs of the app container
	@echo   watch             Watch the source files and rebuild on change
	@echo   mysql             Open a MySQL shell
	@echo   test-mysql        Open a MySQL shell for testing environment
	@echo   prepare-for-test  Prepare test environment
	@echo   test              Run the tests
	@echo   exit-test         Exit the test environmentest

.PHONY:
build :
	@$(compose) build

.PHONY:
build-up :
	@$(compose) up -d --build

.PHONY:
build-down :
	@$(compose) down

.PHONY:
install: composer-install yarn-install yarn-dev

.PHONY:
rebuild: clean build

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
exec:
	@$(exec) -t -i web bash

.PHONY:
mysql:
	@$(exec) -t -i database mysql -u root -pdemo pdfraktor

.PHONY:
test-mysql:
	@$(exec) -t -i database mysql -u root -pdemo pdfraktor_test

.PHONY:
logs:
	@$(exec) web symfony server:log

.PHONY:
prepare-for-test:
	@echo "Preparing test environment"
	@$(exec) -T web rm -rf var/cache/test/*
	@$(exec) -T web composer install
	@$(exec) -T web yarn install
	@$(exec) -T web yarn encore production
	@$(exec) -T web symfony console --env=test doctrine:database:drop --force --if-exists
	@$(exec) -T web symfony console --env=test doctrine:database:create --if-not-exists
	@$(exec) -T web symfony console --env=test doctrine:migrations:migrate -n --allow-no-migration

.PHONY:
test: prepare-for-test test-all

.PHONY:
test-all:
	@$(exec) -T web ./vendor/bin/phpunit

.PHONY:
test-integration:
	@$(exec) -T web ./vendor/bin/phpunit tests/Controller

.PHONY:
test-application:
	@$(exec) -T web ./vendor/bin/phpunit tests/Service

.PHONY:
exit-test:
	@$(exec) web symfony console --env=test doctrine:database:drop --force
	@$(exec) web composer install
	@$(exec) web yarn install
	@$(exec) web yarn encore dev

.PHONY:
composer-cache-dir:
	@$(exec) -T web composer config cache-files-dir

.PHONY:
yarn-cache-dir:
	@$(exec) -T web yarn cache dir

.PHONY:
composer-validate:
	@$(exec) -T web composer validate --strict
