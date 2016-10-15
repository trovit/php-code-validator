<?php

namespace Trovit\PhpCodeValidator\Model\Validators;

use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorResult;

/**
 * Class Validator.
 */
abstract class Validator
{
    /**
     * @param string $code
     *
     * @return PhpCodeValidatorResult
     */
    abstract public function checkCode($code);
}
