.PHONY: clean code-style coverage help static-analysis update-dependencies install-dependencies
.DEFAULT_GOAL := coverage

PHPUNIT =  ./vendor/bin/phpunit -c ./phpunit.xml
PHPDBG =  phpdbg -qrr ./vendor/bin/phpunit -c ./phpunit.xml
PHPSTAN  = ./vendor/bin/phpstan
PHPCS = ./vendor/bin/phpcs --extensions=php
PHPCBF = ./vendor/bin/
INFECTION = ./vendor/bin/infection
CONSOLE = ./bin/console

clean:
	rm -rf ./build ./vendor

code-style:
	${PHPCS} --report-full --report-gitblame --standard=PSR12 ./src

coverage:
	${PHPDBG}

fix-code-style:
	${PHPCBF} src/ --standard=PSR12

infection-testing: coverage
	${INFECTION} --coverage=build/logs/phpunit --min-msi=65 --threads=`nproc`

static-analysis:
	${PHPSTAN} analyse src --no-progress --level=7

update-dependencies:
	composer update

install-dependencies:
	composer install

help:
	# Usage:
	#   make <target> [OPTION=value]
	#
	# Targets:
	#   clean                Cleans the coverage and the vendor directory
	#   code-style           Check codestyle using phpcs
	#   coverage (default)   Generate code coverage (html, clover)
	#   fix-code-style       Fix code style
	#   help                 You're looking at it!
	#   infection-testing    Run infection/mutation testing
	#   static-analysis      Run static analysis using phpstan
	#   update-dependencies  Run composer update
	#   install-dependencies Run composer install
