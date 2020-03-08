PROJECT_NAME=minh
up: build
	docker network create web || true
	docker-compose -p ${PROJECT_NAME} -f environment/development.yml up -d --force-recreate
build:
	docker-compose -p ${PROJECT_NAME} -f environment/development.yml build
file:
	docker exec web-php-fpm bash -c "[[ -f var/data/data.sqlite ]] || touch var/data/data.sqlite"
down:
	docker-compose -p ${PROJECT_NAME} -f environment/development.yml down
migrate:
	docker exec web-php-fpm bash -c "php bin/console doctrine:schema:update --force"
provision: file
	docker exec web-php-fpm bash -c "php composer.phar install"
