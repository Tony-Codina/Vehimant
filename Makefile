.PHONY: diff migrate test dc-up dc-down

diff:
	@bin/console doctrine:migrations:diff --no-interaction --formatted

migrate:
	@bin/console doctrine:migrations:migrate --no-interaction

test:
	@./vendor/bin/phpunit --testdox

dc-up:
	@docker compose up -d

dc-down:
	@docker compose down
