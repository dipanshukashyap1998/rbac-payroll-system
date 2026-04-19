<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## CI/CD

This repository now includes GitHub Actions workflows for validation and deployment:

- `CI` runs on pushes to `main` and `develop`, plus all pull requests. It installs PHP dependencies and runs `php artisan test`.
- `Deploy` runs on pushes to `main` or manually from the Actions tab. It validates the release first, uploads the application to your server over SSH, runs `composer install --no-dev`, executes `php artisan migrate --force`, refreshes Laravel caches, and points the `current` symlink at the new release.

Set these repository or environment secrets before using deployment:

- `SSH_HOST`
- `SSH_PORT`
- `SSH_USER`
- `SSH_PRIVATE_KEY`
- `DEPLOY_PATH`
- `APP_ENV_FILE`

Expected server layout:

- The workflow creates `DEPLOY_PATH/releases`, `DEPLOY_PATH/shared`, and `DEPLOY_PATH/current`.
- `APP_ENV_FILE` is written to `DEPLOY_PATH/shared/.env`.
- Shared writable Laravel storage lives in `DEPLOY_PATH/shared/storage`.

