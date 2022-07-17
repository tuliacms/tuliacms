.PHONY: bash behat deptrac

#ifeq ($(shell test -e tulia-local-composer.json && echo -n yes),yes)
#COMPOSER_JSON = tulia-local-composer.json
#else
COMPOSER_JSON = composer.json
#endif

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

.PHONY: install
install:
	cp .env.dist .env \
    && echo "DATABASE_URL="mysql://root:root@$(shell basename $(CURDIR))_tulia_mysql_1:3306/development?serverVersion=5.7"" >> .env \
    && cp config/dynamic.php.dist config/dynamic.php \
    && $(PHPROOT) composer install \
    && $(PHPROOT) npm i chokidar \
    && $(PHPROOT) cd public/docs \
    && $(PHPROOT) npm install

.PHONY: composer
composer:
	$(PHPROOT) composer "$(ARGS)"

.PHONY: setup
setup:
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
	${PHPROOT} php bin/console doctrine:schema:drop --force --full-database \
	&& ${PHPROOT} php bin/console doctrine:schema:update --force \
	&& ${PHPROOT} php bin/console doctrine:migrations:migrate --all-or-nothing --no-interaction

.SILENT:
