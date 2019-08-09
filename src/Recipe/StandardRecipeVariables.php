<?php

namespace RecipeRunner\RecipeRunner\Recipe;

use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

/**
 * Standard variables for recipes.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class StandardRecipeVariables
{
    /**
     * Returns a collection with a standard variables.
     *
     * @return CollectionInterface
     */
    public static function getCollectionOfVariables(): CollectionInterface
    {
        return new MixedCollection([
            'os_family' => PHP_OS_FAMILY,
            'dir_separator' => DIRECTORY_SEPARATOR,
            'path_separator' => PATH_SEPARATOR,
            'temporal_dir' => sys_get_temp_dir(),
            'php_version' => PHP_VERSION,
        ]);
    }
}
