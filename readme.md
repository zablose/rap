# RaP - Roles and Permissions

[![Build Status](https://travis-ci.org/zablose/rap.svg?branch=master)](https://travis-ci.org/zablose/rap)

## Publish

    php artisan vendor:publish --provider="Zablose\Rap\RapServiceProvider" --tag=config
    php artisan vendor:publish --provider="Zablose\Rap\RapServiceProvider" --tag=migrations

## Development

> Check submodule [readme](https://github.com/zablose/docker-damp/blob/master/readme.md) for more details about
> development environment used.

### Hosts

Append to `/etc/hosts`.

```
127.0.0.1       rap.zdev
127.0.0.1       www.rap.zdev
```

## License

This package is free software distributed under the terms of the MIT license.
