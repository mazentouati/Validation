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

namespace Respect\Validation\Rules;

use ReflectionClass;
use Respect\Validation\Exceptions\ComponentException;

/**
 * @author Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 * @author Henrique Moody <henriquemoody@gmail.com>
 * @author Hugo Hamon <hugo.hamon@sensiolabs.com>
 */
class Zend extends AbstractRule
{
    protected $messages = [];
    protected $zendValidator;

    public function __construct($validator, $params = [])
    {
        if (is_object($validator)) {
            return $this->zendValidator = $validator;
        }

        if (!is_string($validator)) {
            throw new ComponentException('Invalid Validator Construct');
        }

        if (false === mb_stripos($validator, 'Zend')) {
            $validator = "Zend\\Validator\\{$validator}";
        } else {
            $validator = "\\{$validator}";
        }

        $zendMirror = new ReflectionClass($validator);

        if ($zendMirror->hasMethod('__construct')) {
            $this->zendValidator = $zendMirror->newInstanceArgs($params);
        } else {
            $this->zendValidator = $zendMirror->newInstance();
        }
    }

    public function assert($input): void
    {
        $validator = clone $this->zendValidator;

        if ($validator->isValid($input)) {
            return;
        }

        $exceptions = [];
        foreach ($validator->getMessages() as $m) {
            $exceptions[] = $this->reportError($m, get_object_vars($this));
        }

        throw $this->reportError($input)->addChildren($exceptions);
    }

    public function validate($input): bool
    {
        $validator = clone $this->zendValidator;

        return $validator->isValid($input);
    }
}
