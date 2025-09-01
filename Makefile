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
worker:
	@php bin/console messenger:consume async -vv

worker-once:
	@php bin/console messenger:consume async -vv --limit=1 --time-limit=30

worker-setup:
	@php bin/console messenger:setup-transports
