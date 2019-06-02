<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Definition\RecipeMaker;

use InvalidArgumentException;
use RecipeRunner\RecipeRunner\Definition\ActionDefinition;
use RecipeRunner\RecipeRunner\Definition\RecipeDefinition;
use RecipeRunner\RecipeRunner\Definition\StepDefinition;
use RecipeRunner\RecipeRunner\Module\Invocation\Method;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Yosymfony\Collection\MixedCollection;

/**
 * Makes a recipe from a YAML string.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class YamlRecipeMaker
{
    /**
     * Makes a recipe from a YAML string.
     *
     * @param string $content
     *
     * @return RecipeDefinition
     */
    public function makeRecipeFromString(string $content): RecipeDefinition
    {
        $recipe = Yaml::parse($content);

        return $this->makeRecipe($recipe);
    }

    /**
     * Makes a recipe from a .yml file
     *
     * @param string $filename
     *
     * @return RecipeDefinition
     */
    public function makeRecipeFromFile(string $filename): RecipeDefinition
    {
        $recipe = Yaml::parseFile($filename);

        return $this->makeRecipe($recipe);
    }

    private function makeRecipe(array $recipeAsCollection): RecipeDefinition
    {
        $recipeAsCollection = new MixedCollection($recipeAsCollection);
        $name = (string) $recipeAsCollection->getOrDefault('name', 'Recipe with no name');
        $extraDataCollection = new MixedCollection($recipeAsCollection->getOrDefault('extra', []));
        $stepCollection = $this->readSteps($recipeAsCollection);
        $recipeDef = new RecipeDefinition($name, $stepCollection, $extraDataCollection);

        return $recipeDef;
    }

    private function readSteps(MixedCollection $recipe): MixedCollection
    {
        $steps = $this->readArray($recipe, 'steps');
        $stepDefinitions = new MixedCollection();

        foreach ($steps as $key => $step) {
            $step = new MixedCollection($step);
            $name = $step->getOrDefault('name', sprintf('Step: %s', $key));
            $id = $this->generateId('step');
            $stepDefinition = new StepDefinition($id, $this->readActions($step));
            $stepDefinition->setName($name);
            
            $stepDefinition->setWhenExpression($this->readWhenExpression($step))
                ->setLoopExpression($this->readLoopExpression($step));
            
            $stepDefinitions->add($key, $stepDefinition);
        }

        return $stepDefinitions;
    }

    private function readActions(MixedCollection $step): MixedCollection
    {
        $actions = $this->readArray($step, 'actions');
        $actionDefinitions = new MixedCollection();

        foreach ($actions as $key => $action) {
            $action = new MixedCollection($action);

            $name = $action->getOrDefault('name', sprintf('Action: %s', $key));
            $id = $this->generateId('step');

            $actionDefinition = new ActionDefinition($id, $this->readMethod($action));
            $actionDefinition->setName($name);
            $actionDefinition->setWhenExpression($this->readWhenExpression($action))
                ->setLoopExpression($this->readLoopExpression($action))
                ->setVariableName($this->readActionRegister($action));

            $actionDefinitions->add($key, $actionDefinition);
        }

        return $actionDefinitions;
    }

    private function readWhenExpression(MixedCollection $collection): string
    {
        $value = $collection->getOrDefault('when', '');

        if (!\is_string($value)) {
            throw new InvalidArgumentException('Invalid schema: "when" has to be a string expression.');
        }

        return $value;
    }

    /**
     * @return string|MixedCollection
     */
    private function readLoopExpression(MixedCollection $collection)
    {
        $value = $collection->getOrDefault('loop', '');

        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            return new MixedCollection($value);
        }

        throw new InvalidArgumentException('Invalid schema: "loop" has to be a string expression or a list of items.');
    }

    private function readActionRegister(MixedCollection $actionCollection): string
    {
        $value = $actionCollection->getOrDefault('register', '');

        if (!\is_string($value)) {
            throw new InvalidArgumentException('Invalid schema: "register" has to be a string value.');
        }

        return $value;
    }

    private function readMethod(MixedCollection $actionCollection): Method
    {
        $methodCollection = $actionCollection->except(['when', 'loop', 'register', 'name']);
        $counter = count($methodCollection);

        if ($counter == 0 || $counter > 1) {
            throw new InvalidArgumentException('Invalid schema: an action needs one and just one invocation method.');
        }

        $methodName = $methodCollection->keys()->firstOrDefault();
        $method = new Method($methodName);
        
        foreach ((array) $methodCollection->firstOrDefault() as $key => $value) {
            $method->addParameter($key, $value);
        }

        return $method;
    }

    private function readArray(MixedCollection $collection, string $key): array
    {
        $result = $collection->getOrDefault($key);

        if (!\is_array($result)) {
            throw new InvalidArgumentException("Invalid schema: \"{$key}\" has to be an array value.");
        }

        return $result;
    }

    private function generateId(string $prefix): string
    {
        return \uniqid($prefix, true);
    }
}
