.PHONY: bash behat deptrac

PHPROOT = docker exec -it --user "$(id -u):$(id -g)" --workdir="/var/www/html" $(shell basename $(CURDIR))_tulia_www_1
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
    && $(PHPROOT) echo "DATABASE_URL="mysql://root:root@$(shell basename $(CURDIR))_tulia_www_1:3306/development?serverVersion=5.7"" >> .env \
    && $(PHPROOT) composer install \
    && $(PHPROOT) npm i chokidar \
    && $(PHPROOT) cd public/docs \
    && $(PHPROOT) npm install

.PHONY: setup
setup:
	${PHPROOT} php bin/console setup

.PHONY: bash
bash:
	${PHPROOT} /bin/bash

.SILENT:
