.PHONY: help

.DEFAULT_GOAL:=help

SHELL:=/bin/bash

setup:
	docker volume create --name=invoice-mysql-db-share

run: setup ## Run
	set -o allexport; source ./src/.env; docker-compose down; docker-compose up --build

start: run

stop: ## Stop the docker processes
	docker-compose stop

ssh: ## SSH into the web container
		set -o allexport; source ./src/.env; docker-compose exec invoice-web sh

log: ## Tail the Laravel log
	set -o allexport; source ./src/.env; docker-compose exec invoice-web tail -f storage/logs/laravel.log

migrate: ## Migrate the database
	set -o allexport; source ./src/.env; docker-compose exec invoice-web php artisan migrate

rollback: ## Roll back the last migration batch
	set -o allexport; source ./src/.env; docker-compose exec invoice-web php artisan migrate:rollback

fresh: ## Re-create the database and seed development data
	set -o allexport; source ./src/.env; docker-compose exec invoice-web php artisan migrate:fresh --seed

tinker: ## Launch artisan tinker
	set -o allexport; source ./src/.env; docker-compose exec invoice-web php artisan tinker

rebuild: stop ## Rebuilds the web image without cache
	set -o allexport; source ./src/.env; docker-compose build --no-cache invoice-web

fix:
	set -o allexport; source ./src/.env; docker-compose exec -T invoice-web sh -c './vendor/bin/pint ./app/'
	set -o allexport; source ./src/.env; docker-compose exec -T invoice-web sh -c './vendor/bin/rector process app/'

quality:
	#set -o allexport; source ./src/.env; docker-compose exec -T invoice-web composer audit
	set -o allexport; source ./src/.env; docker-compose exec -T invoice-web ./vendor/bin/phpstan analyse --memory-limit=4g
	set -o allexport; source ./src/.env; docker-compose exec -T invoice-web sh -c './vendor/bin/pint --test'
	set -o allexport; source ./src/.env; docker-compose exec -T invoice-web sh -c './vendor/bin/rector process app/ --dry-run'

test: ## Run all tests
	docker-compose exec -T invoice-web sh -c 'php artisan --env=local config:clear'
	docker-compose exec -T invoice-web sh -c 'php artisan --env=testing config:clear'
	docker-compose exec -T invoice-web sh -c 'php artisan --env=testing migrate:fresh'
	docker-compose exec -T invoice-web sh -c './vendor/bin/phpunit'

# -----------------------------------------------------------------------------------

help: ## Prints the help about targets.
	@printf "Usage:             make [\033[34mtarget\033[0m]\n"
	@printf "Default:           \033[34m%s\033[0m\n" $(.DEFAULT_GOAL)
	@printf "Targets:\n"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf " \033[34m%-17s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST) | sort

clearall: ## Clear all caches
	set -o allexport; source ./src/.env; docker-compose exec invoice-web php artisan cache:clear
	set -o allexport; source ./src/.env; docker-compose exec invoice-web php artisan route:clear
	set -o allexport; source ./src/.env; docker-compose exec invoice-web php artisan config:clear
	set -o allexport; source ./src/.env; docker-compose exec invoice-web php artisan view:clear