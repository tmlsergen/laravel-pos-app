.PHONY: composer-install
# composer install with docker
# Usage: make composer-install
composer-install:
	docker run --rm --interactive --tty --volume $(PWD):/app composer install

.PHONY: run-app
# Run the app with docker
# Usage: make run-app
run-app:
	./vendor/bin/sail up -d


.PHONY: stop-app
# Stop the app with docker
# Usage: make stop-app
stop-app:
	./vendor/bin/sail down

.PHONY: migrate
# Run the migration with docker
# Usage: make migrate
migrate:
	./vendor/bin/sail artisan migrate --seed


.PHONY: npm-install
# npm install with docker
# Usage: make npm-install
npm-install:
	./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
