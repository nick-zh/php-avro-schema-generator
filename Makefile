.PHONY: clean code-style coverage help test test-unit test-integration static-analysis update-dependencies
.DEFAULT_GOAL := test

PHPUNIT =  ./vendor/bin/phpunit -c ./phpunit.xml
PHPDBG =  phpdbg -qrr ./vendor/bin/phpunit -c ./phpunit.xml
PHPSTAN  = ./vendor/bin/phpstan
PHPCS = ./vendor/bin/phpcs --extensions=php
CONSOLE = ./bin/console

clean:
	rm -rf ./build ./vendor

code-style:
	${PHPCS} --report-full --report-gitblame --standard=PSR2 ./src

coverage:
	${PHPDBG}

test:
	${PHPUNIT}

test-unit:
	${PHPUNIT} --testsuite=Unit

test-integration:
	${PHPUNIT} --testsuite=Integration

static-analysis:
	${PHPSTAN} analyse src --no-progress --level=7

update-dependencies:
	composer update

help:
	# Usage:
	#   make <target> [OPTION=value]
	#
	# Targets:
	#   clean               Cleans the coverage and the vendor directory
	#   code-style          Check codestyle using phpcs
	#   coverage            Generate code coverage (html, clover)
	#   help                You're looking at it!
	#   test (default)      Run all the tests with phpunit
	#   test-unit           Run all unit tests with phpunit
	#   test-integration    Run all integration tests with phpunit
	#   static-analysis     Run static analysis using phpstan
	#   update-dependencies Run composer update
