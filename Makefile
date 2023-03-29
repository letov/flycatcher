#!/usr/bin/make

docker_bin := $(shell command -v docker 2> /dev/null)
docker_compose_bin := $(shell command -v docker-compose 2> /dev/null)

build:
	$(docker_compose_bin) build

up:
	$(docker_compose_bin) up --no-recreate --detach

down:
	$(docker_compose_bin) down
	$(docker_bin) volume prune --force

shell: up
	$(docker_compose_bin) exec php-fpm bash
