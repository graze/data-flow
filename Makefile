.PHONY: test test-coverage test-unit test-unit-coverage test-functional test-functional-coverage install

SHELL = /bin/sh

DOCKER ?= $(shell which docker)
DOCKER_REPOSITORY := graze/data-flow
VOLUME := /opt/graze/data-flow
VOLUME_MAP := -v $$(pwd):${VOLUME}
DOCKER_RUN := ${DOCKER} run --rm -t ${VOLUME_MAP} ${DOCKER_REPOSITORY}:latest

install: ## Download the dependencies then build the image :rocket:.
	make 'composer-install --optimize-autoloader'
	$(DOCKER) build --tag ${DOCKER_REPOSITORY}:latest .

composer-%: ## Run a composer command, `make "composer-<command> [...]"`.
	${DOCKER} run -t --rm \
        -v $$(pwd):/usr/src/app \
        -v ~/.composer:/root/composer \
        -v ~/.ssh:/root/.ssh:ro \
        graze/composer --ansi --no-interaction $* $(filter-out $@,$(MAKECMDGOALS))

test:
	${DOCKER_RUN} ./vendor/bin/phpunit

test-coverage:
	${DOCKER_RUN} ./vendor/bin/phpunit --coverage-text --coverage-html ./tests/report

test-unit:
	${DOCKER_RUN} ./vendor/bin/phpunit --testsuite unit

test-unit-coverage:
	${DOCKER_RUN} ./vendor/bin/phpunit --testsuite unit --coverage-text --coverage-html ./tests/report

test-functional:
	${DOCKER_RUN} ./vendor/bin/phpunit --testsuite functional

test-functional-coverage:
	${DOCKER_RUN} ./vendor/bin/phpunit --testsuite functional --coverage-text --coverage-html ./tests/report
