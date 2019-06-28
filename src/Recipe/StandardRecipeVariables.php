<?php

namespace RecipeRunner\RecipeRunner\Recipe;

use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

class StandardRecipeVariables
{
    public static function getCollectionOfVariables(): CollectionInterface
    {
        return new MixedCollection([
            'os_family' => PHP_OS_FAMILY,
            'dir_separator' => DIRECTORY_SEPARATOR,
            'path_separator' => PATH_SEPARATOR,
            'temporal_dir' => sys_get_temp_dir(),
        ]);
    }
}