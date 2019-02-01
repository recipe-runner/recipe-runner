<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\IO;

/**
 * Non-interactive interface that never writes the output.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
class NullIO implements IOInterface
{
    /**
     * {@inheritdoc}
     */
    public function isInteractive(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, bool $newline = true, int $verbosity = self::VERBOSITY_NORMAL): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function ask(string $question, string $default = ''): string
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function askConfirmation(string $question, bool $default = true): bool
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function askWithHiddenResponse(string $question): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function askChoice(string $question, array $choices, int $default, int $attempts = self::INFINITE_ATTEMPTS): int
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function askMultiselectChoice(string $question, array $choices, int $default, int $attempts = self::INFINITE_ATTEMPTS): array
    {
        return [$default];
    }
}
