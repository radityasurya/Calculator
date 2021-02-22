.PHONY: run

REPO ?= radityasurya
IMAGE_NAME ?= calculator
VERSION ?= latest

init: build composer-install dump-autoload

build:
	docker build -t ${REPO}/${IMAGE_NAME}:${VERSION} .

composer-install:
	docker run -it --rm -v ${PWD}:/calculator ${REPO}/${IMAGE_NAME}:${VERSION} composer --working-dir=/calculator/ update

dump-autoload:
	docker run -it --rm -v ${PWD}:/calculator ${REPO}/${IMAGE_NAME}:${VERSION} composer --working-dir=/calculator/ -- dump

run:
	docker run -it --rm -v ${PWD}:/calculator ${REPO}/${IMAGE_NAME}:${VERSION} php /calculator/src/index.php

test:
	docker run -it --rm -v ${PWD}:/calculator ${REPO}/${IMAGE_NAME}:${VERSION} php /calculator/vendor/bin/phpunit /calculator/tests

shell:
	docker-compose run calculator bash