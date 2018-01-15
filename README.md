# LaraCRUD

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Provides quick application scaffolding as well as HTML and JSON API interfaces for model CRUD.

## Project Goal
To give you the ability to scaffold your data model in thirty minutes or less by hand or by script.
 
We let you quickly produce everything necessary (models, transformers, repositories, etc.) and wire it together in a 
single console command, instantly exposing your data for Create Read Update and Delete operations via both 
an "Admin Panel" web GUI and a RESTful JSON API.

tl;dr: You write migrations, we give you an admin panel + API

## Project Status
ALPHA

Super early days, feature incomplete, USE AT YOUR OWN RISK.

## Install

Via Composer

``` bash
$ composer require jspaceboots/laracrud
```

## Quickstart

First you need to publish the config and public assets:
``` php
php artisan vendor:publish
```

Next modify your applications config/crud.php to suit the needs of your project. Once you're done:

```php
php artisan laracrud:make:model MySingularModel
```

This will generate the files necessary for LaraCRUD to hook into your data model, specifically a:
- Model
- Repository
- Transformer
- Migration

These will be generated in directories matching the namespaces laid out in config/crud.php. For instance, 
if your repositories namespace is set to "\\App\\Repositories\\" the generated repository will be written to 
app/repositories. The one exception is the migration, which will be placed in database/migrations. In addition 
to generating these files the generated model will be added to the routing configuration in config/crud.php.

From here you only need to fill out the generated migration and then run:

```php
php artisan migrate
```

At this point all non-relational fields of your model should be fully accessible via http://{{domain}}/crud/{{model}} and 
http://{{domain}}/api/crud/{{model}}

## LaraCRUD Conventions

### Table conventions
* LaraCRUD requires the PK of every entity be 'id'.
* LaraCRUD requires FKs to conform to this syntax: {{foreign_model}}_id
    - examples: toaster_id, user_role_id 
* LaraCRUD requires the name of join tables for M:N relations to conform to this syntax: {{model_table_1}}_{{model_table_2}} (order is not important)
    - examples: users_roles, toasters_heating_coils
* LaraCRUD requires that join tables for M:N relations will contain at least two columns: {{foreign_model_1}}_id & {{foreign_model_2}}_id
    - examples: user_id & role_id, toaster_id & heating_coil_id
    
The laracrud:make:model command will generate table names by transforming your camel case model name (MyModel) into an 
underscore seperated representation and pluralizing the last word (my_models).

### Model conventions
* LaraCRUD requires model names to be singular and camel cased
    - examples: User, Toaster, HeatingCoil
* LaraCRUD requires you define an array of validator strings on your model (laracrud:make:model will stub this)
    - see: https://laravel.com/docs/5.5/validation#available-validation-rules
* LaraCRUD supports using UUIDs for model PKs with the installation of: https://github.com/webpatser/laravel-uuid
    - with 'useUuids' enabled in config/crud.php laracrud:make:model will generate models that auto-populate V4 UUIDs as their PK

## Relations
* LaraCRUD requires foreign keys conform to conventions laid out in Table conventions above
* LaraCRUD requires you to define some additional metadata in your Repositories in order to traverse/persist M:N relations and reverse 1:M

## Filters

## Removing the CRUD

If you're using LaraCRUD to bootstrap a project, or just wish to remove LaraCRUD at some future point,
simply run:

```php
php artisan laracrud:eject
```

This will remove the dependancy to LaraCRUDs Abstract classes from the models, repositories, and 
transformers that have been generated, remove the packages published configuration and assets from
your project, and finally de-register the LaraCRUD service provider with your Laravel instance.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [Johnny Spaceboots][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/:vendor/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/:vendor/:package_name/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/:vendor/:package_name.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/:vendor/:package_name.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/:vendor/:package_name.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/:vendor/:package_name
[link-travis]: https://travis-ci.org/:vendor/:package_name
[link-scrutinizer]: https://scrutinizer-ci.com/g/:vendor/:package_name/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/:vendor/:package_name
[link-downloads]: https://packagist.org/packages/:vendor/:package_name
[link-author]: https://github.com/:author_username
[link-contributors]: ../../contributors
