![](https://github.com/zablose/rap/actions/workflows/tests-on-master.yml/badge.svg)
![](https://github.com/zablose/rap/actions/workflows/tests-on-dev.yml/badge.svg)

# RaP - Roles and Permissions

## Publish

    php artisan vendor:publish --provider="Zablose\Rap\RapServiceProvider" --tag=config
    php artisan vendor:publish --provider="Zablose\Rap\RapServiceProvider" --tag=migrations

## Development

> Check submodule [readme](https://github.com/zablose/docker-images/blob/master/readme.md) for more details about
> development environment used.

### Hosts

Append to `/etc/hosts`.

```
127.0.0.24      rap.zdev
```

## License

This package is free software distributed under the terms of the MIT license.
