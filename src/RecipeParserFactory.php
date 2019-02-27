<?php

namespace RecipeRunner\RecipeRunner;

use RecipeRunner\RecipeRunner\Action\ActionParser;
use RecipeRunner\RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;
use RecipeRunner\RecipeRunner\IO\IOInterface;
use RecipeRunner\RecipeRunner\IO\NullIO;
use RecipeRunner\RecipeRunner\Module\BuiltIn\EssentialModule;
use RecipeRunner\RecipeRunner\Module\ModuleMethodExecutor;
use RecipeRunner\RecipeRunner\Recipe\RecipeParser;
use RecipeRunner\RecipeRunner\Step\StepParser;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

class RecipeParserFactory
{
    /**
     * Creates a standard RecipeParser. This method always add the Essential module.
     *
     * @param ModuleInterface[] $modules Collection of modules available for recipes.
     * @param IOInterface $io The input/output.
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
        $recipeParser->setIO($io);

        return $recipeParser;
    }

    private static function composeListOfModules(?CollectionInterface $modules): CollectionInterface
    {
        $basicModules = new MixedCollection([new EssentialModule()]);
        
        return $modules ? $basicModules->union($modules) : $basicModules;
    }
}
