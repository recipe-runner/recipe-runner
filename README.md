# Recipe Runner

[![Build Status](https://img.shields.io/travis/recipe-runner/recipe-runner/master.svg?style=flat-square)](https://travis-ci.org/recipe-runner/recipe-runner)
[![Build status](https://ci.appveyor.com/api/projects/status/jr71nwqnqa5p1gd4?svg=true)](https://ci.appveyor.com/project/yosymfony/recipe-runner)


Recipe Runner is a new way to automate tasks and extend your applications.

## Requires

* PHP +7.2

## Installation

The preferred installation method is [composer](https://getcomposer.org):

```bash
composer require recipe-runner/recipe-runner
```

## Usage

```php
use RecipeRunner\Definition\RecipeMaker;
use RecipeRunner\RecipeRunner\Recipe\StandardRecipeVariables;
use RecipeRunner\RecipeRunner\Setup\QuickStart;

$recipeVariables = StandardRecipeVariables::getCollectionOfVariables();

$recipeMaker = new YamlRecipeMaker();
$recipe = $recipeMaker->makeRecipeFromFile('/path-to-a-recipe.yml');

$recipeParser = QuickStart::Create();
$recipeParser->parse($recipe, $recipeVariables);
```

## Recipe example

Recipes are written in YAML:

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
$ cd recipe-runner
$ composer test
```

## License

This library is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
