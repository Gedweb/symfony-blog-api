PHP_RUN = docker compose exec php

run: build test

build:
	docker compose build
	docker compose up -d
	docker compose exec database wait-mysql.sh
	$(PHP_RUN) composer install
	$(PHP_RUN) bin/console doctrine:database:create
	$(PHP_RUN) bin/console app:schema

test:
	$(PHP_RUN) bin/console doctrine:fixtures:load -q
	$(PHP_RUN) vendor/bin/codecept build
	$(PHP_RUN) vendor/bin/codecept run