# Papertrail PHP Connector

![CodeShip build status](https://codeship.com/projects/31e0e160-6306-0133-32b6-62dbb5275a9f/status?branch=master)

Easily integrate [Papertrail log monitor](https://papertrailapp.com) in your PHP applications. This package provides integration for plain vanilla PHP and Laravel.

## Installation

```bash
composer require stephanecoinon/papertrail
```

## Configuration

It is good practice *not* to include credentials in your code so passwords are not stored in version control. Keeping sensitive info in your code could become an issue when you need to open source or share your code with other developers. For this reason, this package will get your papertrail log server details from environment variables `PAPERTRAIL_HOST` and `PAPERTRAIL_PORT` by default.

## Usage

### Plain Vanilla PHP

```php
require __DIR__ . '/vendor/autoload.php';

use StephaneCoinon\Papertrail\Base as Papertrail;

// Boot using default settings (ie Papertrail log server and port
// from environment variables and no log message prefix).
// It also gives you a monolog instance in the process.
$logger = Papertrail::boot();
```

### Laravel 4
Add these line in your `start/global.php`:

```php
// Pass all parameters
\StephaneCoinon\Papertrail\Laravel4::boot($host, $port, $prefix);
```

or

```php
// Grab log server details from environment variables and use a prefix
\StephaneCoinon\Papertrail\Laravel4::bootWithPrefix('MY_APP');
```


### Laravel 5
Edit `app/Providers/AppServiceProvider.php` and add this line in `boot` method:
```php
\StephaneCoinon\Papertrail\Laravel5::boot();
```

## Tests

First, copy ```.env.dist``` as ```.env``` and set your Papertrail host, port and API key in it.

Then run PHPUnit:

```bash
./vendor/bin/phpunit
```
