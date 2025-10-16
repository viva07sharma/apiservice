A simple Symfony-based API service that demonstrates a Weather API client with a caching layer.

Prerequisites
-------------
- PHP >= 8.2 (for local dev)
- Composer (for local dev)
- Docker (if you want to run the project in a container)

Quick start (local)
-------------------
1. Clone the repo

```bash
git clone https://github.com/viva07sharma/apiservice.git apiservice
cd apiservice
```

2. Install dependencies (local)

```bash
composer install
```

3. Run the Symfony local server (if you have the Symfony CLI else it can be downloaded from https://symfony.com/download)

```bash
symfony server:start
```

The Weather api will be available at http://127.0.0.1:8000/api?latitude=52.52&longitude=13.41&current=temperature_2m&hourly=temperature_2m&forecast_days=1

Run with Docker CLI
----------------------------------

1. Build the image

```bash
docker build -t apiservice:dev .
```

2. Run the container

```bash
docker run -d --name apiservice_dev \
  -p 8000:8000 \
  -v "$(pwd)":/var/www/html:cached \
  -w /var/www/html \
  apiservice:dev \
  php -S 0.0.0.0:8000 -t public
```

Visit http://127.0.0.1:8000/api?latitude=52.52&longitude=13.41&current=temperature_2m&hourly=temperature_2m&forecast_days=10

View logs
---------
Symfony writes logs to `var/log`:

```bash
tail -f var/log/dev.log
```

Or from inside the running container:

```bash
docker exec -it apiservice_dev bash
tail -f var/log/dev.log
```

Run tests
---------
Locally (if dependencies are installed):

```bash
composer test
composer test:coverage
```

Inside a running container (example):

```bash
docker exec -it apiservice_dev bash
APP_ENV=test APP_DEBUG=1  vendor/bin/simple-phpunit --testdox
APP_ENV=test APP_DEBUG=1  php -dxdebug.mode=coverage vendor/bin/simple-phpunit --testdox --coverage-text
```

Notes
-----
- The repo includes unit and functional tests under `tests/Unit` and `tests/Functional`.
- `phpunit.xml.dist` defines separate test suites so you can run `--testsuite unit` / `functional`.
