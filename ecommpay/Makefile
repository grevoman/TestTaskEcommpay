# путь к .env
ENV=--env-file .env
# аргументы переданные вместе с вызовом инструкции
ARGS=$(filter-out $@, $(MAKECMDGOALS))

build:
	docker-compose $(COMPOSE) $(ENV) build

up:
	docker-compose $(COMPOSE) $(ENV) up -d

down:
	docker-compose $(COMPOSE) $(ENV) down

stop:
	docker-compose $(COMPOSE) $(ENV) stop

start:
	docker-compose $(COMPOSE) $(ENV) start

restart:
	docker-compose $(COMPOSE) $(ENV) restart	

ps:
	docker-compose $(COMPOSE) $(ENV) ps

exec:
	docker-compose $(COMPOSE) $(ENV) exec backend bash -c "php yii $(ARGS)"

shell-php:
	docker-compose $(COMPOSE) $(ENV) exec backend bash

shell-db:
	docker-compose $(COMPOSE) $(ENV) exec mysql bash

migrate:
	docker-compose $(COMPOSE) $(ENV) exec backend bash -c "php yii migrate"

migrate-down:
	docker-compose $(COMPOSE) $(ENV) exec backend bash -c "php yii migrate/down $(ARGS)"

migrate-create:
	docker-compose $(COMPOSE) $(ENV) exec backend bash -c "php yii migrate/create $(ARGS)"

composer-install:
	docker-compose $(COMPOSE) $(ENV) exec backend bash -c "composer install"

project-init:
	docker-compose $(COMPOSE) $(ENV) exec backend bash -c "php init --env=Development --overwrite=All --delete=All"
