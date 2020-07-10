# Keep a changelog

[![Build Status](https://travis-ci.org/AMBERSIVE/laravel-keepachangelog.svg?branch=master)](https://travis-ci.org/AMBERSIVE/laravel-keepachangelog)

This package for Laravel is inspired by [Keep a Changelog](https://keepachangelog.com/en/1.0.0/). The purpose of this package is the goal to provide a command line utility to create and update changelog files in a unique way.

## Installation

```bash
composer require ambersive/keepachangelog --dev
```

## Commands

### Add line

The following command will add a line to the "unreleased" block.

```bash
php artisan changelog:add
```

### Release

If you want to release the "unreleased" block:

```bash
php artisan changelog:release
```

## Documentation

Further information about this package can be found [here](docs/overview.md).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Manuel Pirker-Ihl via [manuel.pirker-ihl@ambersive.com](mailto:manuel.pirker-ihl@ambersive.com). All security vulnerabilities will be promptly addressed.

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

