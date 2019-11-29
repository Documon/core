# Core library for Documon

## Requirements

* PHP 7.2+ with pcntl extension
* Node.js 12.0+ with yarn

## Install

```bash
composer require goez/documon-core
```

## Hacking

Install `spatie/phpunit-watcher` globally:

```bash
composer global require spatie/phpunit-watcher
```

Watch modification of source and test cases, then run:

```bash
phpunit-watcher watch
```

Just run test cases:

```bash
vendor/bin/phpunit
```

## License

MIT
