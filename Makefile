COMMAND = docker-compose -f docker-compose.yml

up:
	@echo "==> Building..."
	@$(COMMAND) -f docker-compose.yml up -d --build

down:
	@echo "==> Down..."
	@$(COMMAND) -f docker-compose.yml down

deps_update:
	@echo "==> Composer install..."
	@$(COMMAND) -f docker-compose.yml run --rm --no-deps php /usr/local/bin/composer install

create_database:
	@echo "==> Create database & schema..."
	-$(COMMAND) -f docker-compose.yml run --rm --no-deps php bin/console do:da:dr --force
	@$(COMMAND) -f docker-compose.yml run --rm --no-deps php bin/console do:da:cr
	@$(COMMAND) -f docker-compose.yml run --rm --no-deps php bin/console do:sc:cr

fixtures:
	@echo "==> Run fixtures..."
	@$(COMMAND) -f docker-compose.yml run --rm --no-deps php bin/console doctrine:fixtures:load

migrate_schema:
	@echo "==> Run Migrate"
	@$(COMMAND) -f docker-compose.yml run --rm --no-deps php bin/console doctrine:migrations:migrate --no-interaction

diff_schema:
	@echo "==> Run Diff"
	@$(COMMAND) -f docker-compose.yml run --rm --no-deps php bin/console doctrine:migrations:diff