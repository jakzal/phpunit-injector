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

update-no-dev:
	composer update --prefer-stable --no-dev
.PHONY: update-no-dev

test: vendor cs deptrac phpunit infection
.PHONY: test

test-min: update-min cs deptrac phpunit infection
.PHONY: test-min

cs: tools/php-cs-fixer
	tools/php-cs-fixer --dry-run --allow-risky=yes --no-interaction --ansi fix
.PHONY: cs

cs-fix: tools/php-cs-fixer
	tools/php-cs-fixer --allow-risky=yes --no-interaction --ansi fix
.PHONY: cs-fix

deptrac: tools/deptrac
	tools/deptrac --no-interaction --ansi --formatter-graphviz-display=0
.PHONY: deptrac

infection: tools/infection tools/infection.pubkey
	phpdbg -qrr ./tools/infection --no-interaction --formatter=progress --min-msi=91 --min-covered-msi=91 --only-covered --ansi
.PHONY: infection

phpunit: tools/phpunit
	tools/phpunit
.PHONY: phpunit

tools: tools/php-cs-fixer tools/deptrac tools/infection tools/phpab
.PHONY: tools

clean:
	rm -rf build
.PHONY: clean

package: clean update-no-dev tools/phpab
	$(eval VERSION=$(shell git describe --abbrev=0 --tags 2> /dev/null | sed -e 's/^v//' || echo 'dev'))
	@mkdir -p build/phar
	@cp LICENSE build/phar/
	@sed -e 's/@@version@@/$(VERSION)/g' manifest.xml.in > build/phar/manifest.xml
	@mkdir -p build/phar/zalas-phpunit-injector-extension && cp -r src build/phar/zalas-phpunit-injector-extension/
	@composer show -N -D | grep -v phpunit/phpunit | tr -d ' ' \
	  | xargs -IXXX composer show XXX -t | grep / | sed -e 's/^[| `-]*\([^ ]*\) .*/\1/' | sort -u \
	  | xargs -IXXX rsync -a --relative vendor/XXX build/phar/zalas-phpunit-injector-extension/
	[ -f .travis/phpunit-injector-extension-private.pem ] \
	  && tools/phpab --all --static --once --phar --key .travis/phpunit-injector-extension-private.pem --output build/zalas-phpunit-injector-extension.phar build/phar \
	  || tools/phpab --all --static --once --phar --output build/zalas-phpunit-injector-extension-$(VERSION).phar build/phar
.PHONY: package

vendor: install

vendor/bin/phpunit: install

tools/phpunit: vendor/bin/phpunit
	ln -sf ../vendor/bin/phpunit tools/phpunit

tools/php-cs-fixer:
	curl -Ls http://cs.sensiolabs.org/download/php-cs-fixer-v2.phar -o tools/php-cs-fixer && chmod +x tools/php-cs-fixer

tools/deptrac:
	curl -Ls http://get.sensiolabs.de/deptrac.phar -o tools/deptrac && chmod +x tools/deptrac

tools/infection: tools/infection.pubkey
	curl -Ls https://github.com/infection/infection/releases/download/0.8.1/infection.phar -o tools/infection && chmod +x tools/infection

tools/infection.pubkey:
	curl -Ls https://github.com/infection/infection/releases/download/0.8.1/infection.phar.pubkey -o tools/infection.pubkey

tools/phpab:
	curl -Ls https://github.com/theseer/Autoload/releases/download/1.24.1/phpab-1.24.1.phar -o tools/phpab && chmod +x tools/phpab
