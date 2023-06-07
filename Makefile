.PHONY: cs cs_dry_run test

.DEFAULT_GOAL := help

help:
	@grep -h -e ' ## ' $(MAKEFILE_LIST) | fgrep -v fgrep | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-12s\033[0m %s\n", $$1, $$2}'

cs: ## Fix PHP CS
	vendor/bin/php-cs-fixer fix --verbose

cs_dry_run: ## Test if PHP CS is correct
	vendor/bin/php-cs-fixer fix --verbose --dry-run

test: ## Run the test suite
	vendor/bin/simple-phpunit

phpstan: ## Run static analysis
	vendor/bin/phpstan analyse -c phpstan.neon
