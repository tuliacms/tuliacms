DOT:= .
NOTHING:= ''

CONTAINER_PREFIX:= $(subst $(DOT),$(NOTHING),$(shell basename $(CURDIR)))
PHPROOT = docker exec -it --user "$(id -u):$(id -g)" -e COMPOSER_MEMORY_LIMIT=-1 --workdir="/var/www" ${CONTAINER_PREFIX}-tulia_www-1
ARGS = $(filter-out $@,$(MAKECMDGOALS))

include vendor/tuliacms/cms/Makefile.common

.SILENT::
