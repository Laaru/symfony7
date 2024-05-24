## —— Docker 🐳 ————————————————————————————————————————————————————————————————
pull: ## Pulls and builds the Docker images
	docker compose build --pull --no-cache

build: ## Builds the Docker images
	docker compose build --no-cache

up: ## Start the docker hub in detached mode (no logs)
	docker compose up --detach

down: ## Stop the docker hub
	docker compose down --remove-orphans

reload: down up
rebuild: down build up
init: pull up

## —— Php container ————————————————————————————————————————————————————————————————
sh: ## Connect to the FrankenPHP container
	docker compose exec php sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	docker compose exec php bash

own: ## Own newly generated files
	docker compose exec php chown -R $$(id -u):$$(id -g) .

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Example: make composer arg="-v"
	@$(eval arg ?=)
	docker compose exec php composer $(arg)

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
symfony: ## Example: make symfony arg="-V"
	@$(eval arg ?=)
	docker compose exec php php bin/console $(arg)

symfony-list:
	docker compose exec php php bin/console list
