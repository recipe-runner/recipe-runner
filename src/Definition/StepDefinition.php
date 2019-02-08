<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Definition;

use InvalidArgumentException;
use Yosymfony\Collection\CollectionInterface;
use Yosymfony\Collection\MixedCollection;

/**
 * Step definition.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class StepDefinition extends BaseBlockDefinition
{
    /** @var CollectionInterface */
    private $actionDefinitions;

    /**
     * Constructor.
     *
     * @param string $name The name of the step.
     * @param ActionDefinition[] $actionDefinitions
     */
    public function __construct(string $name, CollectionInterface $actionDefinitions)
    {
        parent::__construct($name);

        if ($actionDefinitions->isEmpty()) {
            throw new InvalidArgumentException('Any step needs at least one action.');
        }

        $this->actionDefinitions = $actionDefinitions;
    }

    /**
     * Returns the list of activities that compose this step.
     *
     * @return ActionDefinition[]
     */
    public function getActionDefinitions(): CollectionInterface
    {
        return $this->actionDefinitions;
    }
}
