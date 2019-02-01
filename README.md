# Recipe Runner

[![Latest Version on Packagist](https://img.shields.io/packagist/v/recipe-runner/recipe-runner.svg?style=flat-square)](https://packagist.org/packages/recipe-runner/recipe-runner)
[![Build Status](https://img.shields.io/travis/recipe-runner/recipe-runner/master.svg?style=flat-square)](https://travis-ci.org/recipe-runner/recipe-runner)

Recipe Runner is a new way to automate task and extend your applications.

## Requires

* PHP +7.2

## Installation

The preferred installation method is [composer](https://getcomposer.org):

```bash
composer require recipe-runner/recipe-runner
```

## Usage

```php
use RecipeRunner\Recipe\RecipeParser;

$recipeVariables = new MixedCollection();
$recipeParser = RecipeParser::Create();

$recipeParser->parse($recipe, $recipeVariables);
```

## Recipe example

```yaml
name: "Very simple example that creates variables"

steps:
    - actions:
        - register_variables:
            user: "victor"
          register: my_variables
```

## Unit tests

You can run the unit tests with the following command:

```bash
$ cd collection
$ composer test
```

## License

This library is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
