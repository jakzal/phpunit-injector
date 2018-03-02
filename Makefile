default: build

build: install test
.PHONY: build

install:
	composer install
.PHONY: install

update:
	composer update
.PHONY: update

update-min:
	composer update --prefer-stable --prefer-lowest
.PHONY: update-min

test: vendor cs deptrac phpunit infection
.PHONY: test

test-min: update-min cs deptrac phpunit infection
.PHONY: test-min

cs: vendor/bin/php-cs-fixer
	vendor/bin/php-cs-fixer --dry-run --allow-risky=yes --no-interaction --ansi fix
.PHONY: cs

cs-fix: vendor/bin/php-cs-fixer
	vendor/bin/php-cs-fixer --allow-risky=yes --no-interaction --ansi fix
.PHONY: cs-fix

deptrac: vendor/bin/deptrac
	vendor/bin/deptrac --no-interaction --ansi --formatter-graphviz-display=0
.PHONY: deptrac

infection: vendor/bin/infection vendor/bin/infection.pubkey
	phpdbg -qrr ./vendor/bin/infection --no-interaction --formatter=progress --min-msi=98 --min-covered-msi=98 --ansi
.PHONY: infection

phpunit: vendor/bin/phpunit
	vendor/bin/phpunit
.PHONY: phpunit

tools: vendor/bin/php-cs-fixer vendor/bin/deptrac vendor/bin/infection
.PHONY: tools

vendor: install

vendor/bin/phpunit: install

vendor/bin/php-cs-fixer:
	curl -Ls http://cs.sensiolabs.org/download/php-cs-fixer-v2.phar -o vendor/bin/php-cs-fixer && chmod +x vendor/bin/php-cs-fixer

vendor/bin/deptrac:
	curl -Ls http://get.sensiolabs.de/deptrac.phar -o vendor/bin/deptrac && chmod +x vendor/bin/deptrac

vendor/bin/infection: vendor/bin/infection.pubkey
	curl -Ls https://github.com/infection/infection/releases/download/0.8.0/infection.phar -o vendor/bin/infection && chmod +x vendor/bin/infection

vendor/bin/infection.pubkey:
	curl -Ls https://github.com/infection/infection/releases/download/0.8.0/infection.phar.pubkey -o vendor/bin/infection.pubkey
