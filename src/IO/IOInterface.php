<?php

/*
 * This file is part of the "Recipe Runner" project.
 *
 * (c) Víctor Puertas <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RecipeRunner\RecipeRunner\IO;

/**
 * The input/output interface.
 *
 * @author Víctor Puertas <vpgugr@gmail.com>
 */
interface IOInterface
{
    /** @var int Do not output any message*/
    const VERBOSITY_QUIET = 1;

    /** @var int Normal behavior*/
    const VERBOSITY_NORMAL = 2;

    /** @var int Increase verbosity of messages*/
    const VERBOSITY_VERBOSE = 4;
    
    /** @var int Display also the informative non essential messages*/
    const VERBOSITY_VERY_VERBOSE = 8;

    /** @var int Display all messages (debug errors)*/
    const VERBOSITY_VERY_VERY_VERBOSE = 16;

    const INFINITE_ATTEMPTS = -1;

    /**
     * Asks a question to the user.
     *
     * @param string $question The question to ask.
     * @param string $default  The default answer if none is given by the user.
     *
     * @return string The user answer.
     */
    public function ask(string $question, string $default = ''): string;

    /**
     * Asks a confirmation to the user.
     *
     * The question will be answered by yes or no.
     *
     * @param string $question The question to ask.
     * @param bool   $default  The default answer if the user enters nothing.
     *
     * @return bool
     */
    public function askConfirmation(string $question, bool $default = true): bool;

    /**
     * Asks a question to the user and hide the answer.
     *
     * @param string $question The question to ask.
     *
     * @return string The response.
     *
     * @throws RuntimeException If the system does not support hidden responses.
     */
    public function askWithHiddenResponse(string $question): string;

    /**
     * Asks the user to select a value.
     *
     * @param string $question The question to ask.
     * @param array $Choices List of choices to pick from.
     * @param bool|string $default The default answer if the user enters nothing.
     * @param int $attempts Max number of times to ask before giving up. INFINITE_ATTEMPTS in case no limits.
     *
     * @return string The selected value (the key of the choices array).
     */
    public function askChoice(string $question, array $choices, $default, int $attempts = self::INFINITE_ATTEMPTS): string;

    /**
     * Asks the user to select a value.
     *
     * @param string $question The question to ask.
     * @param array $Choices List of choices to pick from.
     * @param  bool|string $default The default answer if the user enters nothing.
     * @param int $attempts Max number of times to ask before giving up. INFINITE_ATTEMPTS in case no limit.
     *
     * @return array The selected values (the keys of the choices array).
     */
    public function askMultiselectChoice(string $question, array $choices, $default, int $attempts = self::INFINITE_ATTEMPTS): array;

    /**
     * Is this input means interactive?
     *
     * @return bool
     */
    public function isInteractive(): bool;
    
    /**
     * Write a message to the output.
     *
     * @param string|array $messages The message as an array of lines or a single string.
     * @param bool $newline Whether to add a new line or not.
     * @param int $verbosity Verbosity level. See VERBOSITY_* constants.
     */
    public function write($messages, bool $newline = true, int $verbosity = self::VERBOSITY_NORMAL): void;
}
