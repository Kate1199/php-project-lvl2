lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
install:
	composer install
test:
	composer exec --verbose phpunit tests -- --coverage-clover phpunit.xml
