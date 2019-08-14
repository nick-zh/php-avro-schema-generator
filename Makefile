.PHONY: clean code-style coverage help test test-unit test-integration static-analysis update-dependencies
.DEFAULT_GOAL := coverage

PHPUNIT =  ./vendor/bin/phpunit -c ./phpunit.xml
PHPDBG =  phpdbg -qrr ./vendor/bin/phpunit -c ./phpunit.xml
PHPSTAN  = ./vendor/bin/phpstan
PHPCS = ./vendor/bin/phpcs --extensions=php
PHPCBF = ./vendor/bin/phpcbf
CONSOLE = ./bin/console

clean:
	rm -rf ./build ./vendor

code-style:
	${PHPCS} --report-full --report-gitblame --standard=PSR2 ./src

coverage:
	${PHPDBG}

fix-code-style:
	${PHPCBF} src/ --standard=PSR12

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
	#   coverage (default)  Generate code coverage (html, clover)
	#   fix-code-style      Fix code style
	#   help                You're looking at it!
	#   static-analysis     Run static analysis using phpstan
	#   update-dependencies Run composer update
