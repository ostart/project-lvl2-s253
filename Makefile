#Makefile

install:
	composer install
	
test:
	composer run-script phpunit tests

lint:
	composer run-script phpcs -- --standard=PSR2 src tests

lint-fix:
	composer run-script phpcbf -- --standard=PSR2 src bin
