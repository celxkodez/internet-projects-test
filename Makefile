# Executables (local)
DOCKER_COMPOSE = docker-compose

# Docker containers
APP = $(DOCKER_COMPOSE) exec app

# Executables
PHP      = $(APP) php
COMPOSER = $(APP) composer
SYMFONY  = $(PHP) bin/console
ANALYSER  = $(PHP) ./vendor/bin/phpstan analyse

# Misc
.DEFAULT_GOAL = help

## ————— Pet Shop Docker Makefile —————————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMPOSE) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMPOSE) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMPOSE) down --remove-orphans

stop: ## Stop the docker hub
	@$(DOCKER_COMPOSE) stop

logs: ## Show live logs
	@$(DOCKER_COMPOSE) logs --tail=0 --follow

sh: ## Connect to the PHP FPM container
	@$(APP) sh

## —— Composer ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "p=" to run a given command, example: make composer p='update'
	@$(eval p ?=)
	@$(COMPOSER) $(p)

vendor: ## Install vendors according to the current composer.lock file
vendor: p=install --prefer-dist --no-progress --no-scripts --no-interaction
vendor: composer
