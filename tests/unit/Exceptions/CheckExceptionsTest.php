<?php

/*
 * This file is part of Respect/Validation.
 *
 * (c) Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Respect\Validation\Exceptions;

use DirectoryIterator;
use ReflectionClass;
use Respect\Validation\Test\TestCase;

/**
 * @coversNothing
 *
 * @author Andy Wendt <andy@awendt.com>
 * @author Augusto Pascutti <augusto@phpsp.org.br>
 * @author Gabriel Caruso <carusogabriel34@gmail.com>
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
class CheckExceptionsTest extends TestCase
{
    protected $deprecateds = [];

    public function provideListOfRuleNames()
    {
        $rulesDirectory = 'library/Rules';
        $rulesDirectoryIterator = new DirectoryIterator($rulesDirectory);
        $ruleNames = [];
        foreach ($rulesDirectoryIterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                continue;
            }

            $ruleName = mb_substr($fileInfo->getBasename(), 0, -4);
            $ruleIsDeprecated = in_array($ruleName, $this->deprecateds);
            $isRuleClassFile = (bool) ('php' !== $fileInfo->getExtension());
            if ($ruleIsDeprecated || $isRuleClassFile) {
                continue;
            }

            $className = 'Respect\\Validation\\Rules\\'.$ruleName;
            $reflectionClass = new ReflectionClass($className);
            if ($reflectionClass->isAbstract() || $reflectionClass->isInterface()) {
                continue;
            }

            $ruleNames[] = [$ruleName];
        }

        return $ruleNames;
    }

    /**
     * @dataProvider provideListOfRuleNames
     *
     * @test
     */
    public function ruleHasAnExceptionWhichHasValidApi($ruleName): void
    {
        $exceptionClass = 'Respect\\Validation\\Exceptions\\'.$ruleName.'Exception';
        self::assertTrue(
            class_exists($exceptionClass),
            sprintf('Expected exception class to exist: %s.', $ruleName)
        );

        $reflectionClass = new ReflectionClass($exceptionClass);
        self::assertTrue(
            $reflectionClass->isSubclassOf(ValidationException::class),
            'Every Respect/Validation exception must extend ValidationException.'
        );
    }
}
