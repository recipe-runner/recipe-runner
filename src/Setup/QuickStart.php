<?php

namespace RecipeRunner\RecipeRunner\Setup;

use RecipeRunner\RecipeRunner\Block\Action\ActionParser;
use RecipeRunner\RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;
use RecipeRunner\RecipeRunner\IO\IOInterface;
use RecipeRunner\RecipeRunner\IO\NullIO;
use RecipeRunner\RecipeRunner\Module\BuiltIn\EssentialModule;
use RecipeRunner\RecipeRunner\Module\ModuleMethodExecutor;
use RecipeRunner\RecipeRunner\Recipe\RecipeParser;
use RecipeRunner\RecipeRunner\Block\Step\StepParser;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

/**
 * Builds a working recipe parser with minimum fuss.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class QuickStart
{
    /**
     * Creates a default RecipeParser that you can get started with.
     * This method always add the Essential module.
     *
     * @param ModuleInterface[] $modules Collection of modules available for recipes.
     * @param IOInterface $io The input/output.
     *
     * @return RecipeParser
     */
    public static function create(CollectionInterface $modules = null, IOInterface $io = null): RecipeParser
    {
        $io = $io ?? new NullIO();
        $finalModules = self::composeListOfModules($modules);
        $expressionResolver = new SymfonyExpressionLanguage();
        $methodExecutor = new ModuleMethodExecutor($finalModules, $expressionResolver, $io);
        $actionParser = new ActionParser($expressionResolver, $methodExecutor);
        $stepParser = new StepParser($actionParser, $expressionResolver);
        $recipeParser = new RecipeParser($stepParser);

        return $recipeParser;
    }

    private static function composeListOfModules(?CollectionInterface $modules): CollectionInterface
    {
        $finalModules = [new EssentialModule()];
        
        if ($modules !== null) {
            foreach ($modules as $module) {
                $finalModules[] = $module;
            }
        }

        return new MixedCollection($finalModules);
    }
}
