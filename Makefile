SHELL = /bin/sh

DOCKER ?= $(shell which docker)
DOCKER_REPOSITORY := graze/dataFlow
VOLUME := /opt/graze/dataFlow
VOLUME_MAP := -v $$(pwd):${VOLUME}
DOCKER_RUN := ${DOCKER} run --rm -t ${VOLUME_MAP} ${DOCKER_REPOSITORY}:latest

.PHONY: install composer clean help
.PHONY: test test-unit test-integration test-matrix

.SILENT: help

install: ## Download the dependencies then build the image :rocket:.
	make 'composer-install --optimize-autoloader --ignore-platform-reqs'
	$(DOCKER) build --tag ${DOCKER_REPOSITORY}:latest .

composer-%: ## Run a composer command, `make "composer-<command> [...]"`.
	${DOCKER} run -t --rm \
        -v $$(pwd):/usr/src/app \
        -v ~/.composer:/root/composer \
        -v ~/.ssh:/root/.ssh:ro \
        graze/composer --ansi --no-interaction $* $(filter-out $@,$(MAKECMDGOALS))

test: ## Run the unit and integration testsuites.
test: lint test-unit test-integration

lint: ## Run phpcs against the code.
	$(DOCKER_RUN) composer lint --ansi

test-unit: ## Run the unit testsuite.
	$(DOCKER_RUN) composer test:unit --ansi

test-matrix: ## Run the tests against multiple targets
    ${DOCKER} run --rm -t ${VOLUME_MAP} -w ${VOLUME} php:5.6-cli \
    vendor/bin/phpunit --testsuite unit
    ${DOCKER} run --rm -t ${VOLUME_MAP} -w ${VOLUME} php:7.0-cli \
    vendor/bin/phpunit --testsuite unit
    ${DOCKER} run --rm -t ${VOLUME_MAP} -w ${VOLUME} diegomarangoni/hhvm:cli \
    vendor/bin/phpunit --testsuite unit

test-integration: ## Run the integration testsuite
	$(DOCKER_RUN) vendor/bin/phpunit --testsuite integration

test-coverage: ## Run all tests and output coverage to the console
	$(DOCKER_RUN) composer test:coverage --ansi

test-coverage-clover: ## Run all tests and output clover coverage to file
	$(DOCKER_RUN) composer test:coverage-clover --ansi

clean: ## Clean up any images.
	$(DOCKER) rmi ${DOCKER_REPOSITORY}:latest

run: ## Run a command on the docker image
	$(DOCKER_RUN) $(filter-out $@,$(MAKECMDGOALS))

help: ## Show this help message.
	echo "usage: make [target] ..."
	echo ""
	echo "targets:"
	fgrep --no-filename "##" $(MAKEFILE_LIST) | fgrep --invert-match $$'\t' | sed -e 's/: ## / - /'
