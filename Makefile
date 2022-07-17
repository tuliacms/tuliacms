.PHONY: bash behat deptrac

ifeq ($(shell test -e tulia-local-composer.json && echo -n yes),yes)
COMPOSER_JSON = tulia-local-composer.json
else
COMPOSER_JSON = composer.json
endif

PHPROOT = docker exec -it --user "$(id -u):$(id -g)" -e COMPOSER_MEMORY_LIMIT=-1 -e COMPOSER=$(COMPOSER_JSON) --workdir="/var/www/html" $(shell basename $(CURDIR))_tulia_www_1
ARGS = $(filter-out $@,$(MAKECMDGOALS))





.PHONY: build
build:
	docker-compose build --build-arg USER_ID=1000 --build-arg GROUP_ID=1000

.PHONY: rebuild
rebuild:
	docker-compose build --no-cache --build-arg USER_ID=1000 --build-arg GROUP_ID=1000

.PHONY: up
up:
	docker-compose up -d --no-build

.PHONY: down
down:
	docker-compose stop

.PHONY: restart
restart:
	docker-compose restart

.PHONY: composer
composer:
	$(PHPROOT) composer "$(ARGS)"

.PHONY: setup-tulia
setup-tulia:
	${PHPROOT} php bin/console setup -v

.PHONY: bash
bash:
	${PHPROOT} /bin/bash

.PHONY: cc
cc:
	${PHPROOT} php bin/console cache:clear -vvv

.PHONY: console
console:
	${PHPROOT} php bin/console "$(ARGS)"

.PHONY: recreate-local-database
recreate-local-database:
	echo "Executing: \e[94mDropping database...\e[0m" \
	&& ${PHPROOT} php bin/console doctrine:schema:drop --force --full-database -q \
	&& echo "Executing: \e[94mRecreating schema...\e[0m" \
	&& ${PHPROOT} php bin/console doctrine:schema:update --force -q \
	&& echo "Executing: \e[94mExecuting migrations...\e[0m" \
	&& ${PHPROOT} php bin/console doctrine:migrations:migrate --all-or-nothing --no-interaction -q

# Subcommands for setup new installation

.PHONY: setup-install
setup-install:
	cp .env.dist .env \
    && echo "DATABASE_URL="mysql://root:root@$(shell basename $(CURDIR))_tulia_mysql_1:3306/development?serverVersion=5.7"" >> .env \
    && cp config/dynamic.php.dist config/dynamic.php \
    && $(PHPROOT) composer install -q

.PHONY: setup
setup:
	echo "Executing: \e[94mBuilding containers...\e[0m" \
	&& docker-compose build --quiet --build-arg USER_ID=1000 --build-arg GROUP_ID=1000\
	&& echo "Executing: \e[94mStarting containers...\e[0m" \
	&& make up \
	&& echo "Executing: \e[94mInstalling composer dependencies...\e[0m" \
	&& make setup-install \
	&& echo "Executing: \e[94mCreating local database...\e[0m" \
    && make recreate-local-database \
	&& echo "Executing: \e[94mIt's time to setup...\e[0m" \
    && make setup-tulia

.SILENT:
