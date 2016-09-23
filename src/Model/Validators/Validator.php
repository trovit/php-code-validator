<?php

namespace Trovit\PhpCodeValidator\Model\Validators;

/**
 * Class Validator
 * @package Trovit\PhpCodeValidator\Model
 */
abstract class Validator
{
    /**
     * @param string $code
     */
    public abstract function checkCode($code);
}
