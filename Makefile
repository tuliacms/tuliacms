DOT:= .
NOTHING:=

CONTAINER_PREFIX:= $(subst $(DOT),$(NOTHING),$(shell basename $(CURDIR)))
PHPROOT       = DOCKER_BUILDKIT=1 HOME=${HOME} docker compose -f docker-compose.yml exec -it -e COMPOSER_MEMORY_LIMIT=-1 tulia_www
PHPROOT_NOTTY = DOCKER_BUILDKIT=1 HOME=${HOME} docker compose -f docker-compose.yml exec -i  -e COMPOSER_MEMORY_LIMIT=-1 tulia_www
ARGS = $(filter-out $@,$(MAKECMDGOALS))

include .makefile.parts/Makefile.docker
include .makefile.parts/Makefile.console
include .makefile.parts/Makefile.setup
include .makefile.parts/Makefile.deploy
include .makefile.parts/Makefile.common

.SILENT::
