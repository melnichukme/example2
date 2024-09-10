compose_file=docker-compose.yml
-include .env.production
-include .env.dev

dir=${CURDIR}
ci_registry=${CI_REGISTRY}
ci_registry_image=${CI_REGISTRY_IMAGE}
ci_registry_image_dev=${CI_REGISTRY_IMAGE_DEV}
ci_registry_user=${CI_REGISTRY_USER}
ci_registry_pass=${CI_REGISTRY_PASS}
c_file=${compose_file}

# Global
install: env-create build up composer-install generate-ssl npm-install npm-build migrate
deploy: deploy-pull build restart composer-install generate-ssl npm-install-prom npm-build-prom migrate
deploy_dev: deploy-pull-dev build restart composer-install generate-ssl npm-install-prom npm-build-prom migrate
update-dev: git-pull migrate npm-install-all npm-build cache-clear

up:
	@docker compose -f ${c_file} up -d
down:
	@docker compose -f ${c_file} down
restart:
	@docker compose -f ${c_file} down
	@docker compose -f ${c_file} up -d

deploy-pull:
	@docker  login -u ${ci_registry_user} -p ${ci_registry_pass} ${ci_registry}
	@docker  pull ${ci_registry}/${ci_registry_image}

deploy-pull-dev:
	@docker  login -u ${ci_registry_user} -p ${ci_registry_pass} ${ci_registry}
	@docker  pull ${ci_registry}/${ci_registry_image_dev}

build:
	@docker compose -f ${c_file} build

env-create:
	@cp .env.example .env

git-pull:
	git pull

job-start:
	@make exec cmd="php bin/console messenger:consume publish"

composer-install:
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader"
composer-install-no-dev:
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-dev"
composer-update:
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer update"

create-database:
	@make exec cmd="php bin/console doctrine:database:create"
reset-database:
	@make exec cmd="php bin/console doctrine:database:drop --force"
	@make exec cmd="php bin/console doctrine:database:create"

migrate:
	@make exec cmd="php bin/console doctrine:migrations:migrate --no-interaction"
migrate-diff:
	@make exec cmd="php bin/console doctrine:migrations:diff"
migrate-status:
	@make exec cmd="php bin/console doctrine:migrations:status"

fixtures-load:
	@make exec cmd="php bin/console doctrine:fixtures:load --purge-exclusions=country --no-interaction"
import:
	@make exec cmd="php bin/console app.arc-import 9_2024_02_05_artists 9_2024_02_05_isrc 9_2024_02_05_noisrc"

cache-clear:
	@make exec cmd="php bin/console cache:clear"
generate-ssl:
	@make exec cmd="php bin/console lexik:jwt:generate-keypair --overwrite"

# Frontend
npm-install-all:
	@make exec-root cmd="npm install"
npm-install:
	@make exec-root cmd="npm install $(pkg)"
npm-install-prom:
	@make exec cmd="npm install  --cache=/npm_cache"
npm-remove:
	@make exec-root cmd="npm remove $(pkg)"
npm-build:
	@make exec cmd="npm run build"
npm-build-prom:
	@make exec cmd="npm run build --cache=/npm_cache"
npm-watch:
	@make exec cmd="npm run watch"

# Terminal
exec:
	@docker compose -f ${c_file} exec php-fpm $$cmd
exec-root:
	@docker compose -f ${c_file} exec -u root php-fpm $$cmd
exec-bash:
	@docker compose -f ${c_file} exec php-fpm bash -c "$(cmd)"
terminal:
	@docker compose -f ${c_file} exec php-fpm bash
