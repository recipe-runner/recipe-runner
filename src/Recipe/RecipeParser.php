<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\Recipe;

use RecipeRunner\Action\ActionParser;
use RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;
use RecipeRunner\Definition\RecipeDefinition;
use RecipeRunner\Expression\ExpressionResolverInterface;
use RecipeRunner\IO\IOAwareInterface;
use RecipeRunner\IO\IOInterface;
use RecipeRunner\IO\IOTrait;
use RecipeRunner\IO\NullIO;
use RecipeRunner\Module\BuiltIn\EssentialModule;
use RecipeRunner\Module\ModuleMethodExecutor;
use RecipeRunner\RecipeVariablesContainer;
use RecipeRunner\Step\StepParser;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

class RecipeParser implements IOAwareInterface
{
    use IOTrait;
    
    /** @var StepParser */
    private $stepParser;

    /**
     * Constructor.
     *
     * @param StepParser $stepParser The step parser.
     */
    public function __construct(StepParser $stepParser)
    {
        $this->stepParser = $stepParser;
    }

    /**
     * Parses a recipe definition.
     *
     * @param RecipeDefinition $recipe The recipe definition.
     * @param CollectionInterface $recipeVariables Collection of variables available during the process.
     *
     * @return StepResult[]
     */
    public function parse(RecipeDefinition $recipe, CollectionInterface $recipeVariables): CollectionInterface
    {
        $this->getIO()->write("Parsing recipe \"{$recipe->getName()}\".");

        $stepResults = new MixedCollection();
        $recipeVariablesContainer = new RecipeVariablesContainer($recipeVariables);

        foreach ($recipe->getStepDefinitions() as $key => $step) {
            $stepResults->add($key, $this->stepParser->parse($step, $recipeVariablesContainer));
        }

        return $stepResults;
    }

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
        self::setUpModules($finalModules, $expressionResolver, $io);
        $methodExecutor = new ModuleMethodExecutor($finalModules);
        $actionParser = new ActionParser($expressionResolver, $methodExecutor);
        $stepParser = new StepParser($actionParser, $expressionResolver);
        
        $recipeParser = new self($stepParser);
        $recipeParser->setIO($io);

        return $recipeParser;
    }

    private static function setUpModules(CollectionInterface $modules, ExpressionResolverInterface $expressionResolver, IOInterface $io)
    {
        foreach ($modules as $module) {
            $module->setExpressionResolver($expressionResolver);
            $module->setIO($io);
        }
    }

    private static function composeListOfModules(?CollectionInterface $modules): CollectionInterface
    {
        $basicModules = new MixedCollection([new EssentialModule()]);
        
        return $modules ? $basicModules->union($modules) : $basicModules;
    }
}
