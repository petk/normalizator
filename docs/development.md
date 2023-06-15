# Normalizator development

## Building `normalizator.phar`

After cloning the Git repository:

```sh
git clone https://github.com/petk/normalizator
cd normalizator
```

Install Composer dependencies:

```sh
composer install
```

To build a `normalizator.phar` file:

```sh
./bin/build
```

## Tests

Tests can be run in development with `phpunit`:

```sh
./vendor/bin/phpunit --display-warnings
```

PHPStan analysis can be executed in development:

```sh
./vendor/bin/phpstan analyse
```

## Building Docker image

To build Docker image run:

```sh
make build-docker
```

[Goss](https://github.com/goss-org/goss) is used for testing Docker image:

```sh
make test-docker
```
