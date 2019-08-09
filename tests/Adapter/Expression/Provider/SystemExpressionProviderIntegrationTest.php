<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\Test\Adapter\Expression\Provider;

use PHPUnit\Framework\TestCase;
use RecipeRunner\RecipeRunner\Adapter\Expression\SymfonyExpressionLanguage;
use Yosymfony\Collection\MixedCollection;

class SystemExpressionProviderIntegrationTest extends TestCase
{
    /** @var SymfonyExpressionLanguage */
    private $expressionResolver;

    public function setUp(): void
    {
        $this->expressionResolver = new SymfonyExpressionLanguage();
    }

    public function testEnvMustReturnTheValueOfAnEnvironmentVariable(): void
    {
        \putenv('name=Víctor');
        
        $result = $this->expressionResolver->resolveStringInterpolation('hi {{env("name")}}', new MixedCollection());

        $this->assertEquals('hi Víctor', $result);
    }

    public function testVersionCompare(): void
    {
        $result = $this->expressionResolver->resolveExpression('version_compare("1.0.0", ">", "0.1.9")', new MixedCollection());

        $this->assertTrue($result);
    }
}
