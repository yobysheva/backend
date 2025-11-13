PHP=php
COMPOSER=composer
ENV=test
DB_USER=test_db
DB_PASS=test_password
DB_NAME=test_db
DB_HOST=localhost
DB_PORT=5432


export APP_ENV=$(ENV)
export APP_SECRET=test_secret_key_for_local
export DEFAULT_URI=http://localhost
export DATABASE_URL=postgresql://$(DB_USER):$(DB_PASS)@$(DB_HOST):$(DB_PORT)/$(DB_NAME)?serverVersion=16&charset=utf8
export MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
export MAILER_DSN=null://null


install:
	@echo Installing composer dependencies...
	$(COMPOSER) install --prefer-dist --no-interaction --optimize-autoloader
	@echo Clearing cache...
	$(PHP) bin/console cache:clear --env=$(ENV)


clean:
	@echo Clearing cache and logs...
	$(PHP) bin/console cache:clear --env=$(ENV) --no-interaction
	if exist var\log\*.log del /Q var\log\*.log


lint:
	@echo Running PHP CS Fixer...
	vendor\bin\php-cs-fixer fix --dry-run --diff --allow-risky=yes
	@echo Running PHP CodeSniffer...
	vendor\bin\phpcs
	@echo Running Psalm...
	vendor\bin\psalm


test:
	@echo Preparing test database...
	@powershell -Command "try { psql -h $(DB_HOST) -U $(DB_USER) -c 'DROP DATABASE IF EXISTS $(DB_NAME)_test;' } catch {}"
	@powershell -Command "psql -h $(DB_HOST) -U $(DB_USER) -c 'CREATE DATABASE $(DB_NAME)_test;'"
	@echo Running migrations...
	$(PHP) bin/console doctrine:migrations:migrate --no-interaction --env=$(ENV)
	@echo Running PHPUnit...
	$(PHP) vendor\bin\phpunit --testdox


ci: clean install lint test


deploy:
	git pull origin main
	$(COMPOSER) install --no-dev --optimize-autoloader
	$(PHP) bin/console cache:clear --env=prod
	$(PHP) bin/console doctrine:migrations:migrate --no-interaction --env=prod


.PHONY: install clean lint test ci deploy
