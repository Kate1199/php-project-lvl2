lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
install:
	composer install
test:
	./vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests
