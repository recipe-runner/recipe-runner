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
        $actionParser->setIO($io);
        $stepParser = new StepParser($actionParser, $expressionResolver);
        $stepParser->setIO($io);
        
        $recipeParser = new RecipeParser($stepParser);
        $recipeParser->setIO($io);

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
