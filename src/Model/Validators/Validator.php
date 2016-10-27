<?php

namespace Trovit\PhpCodeValidator\Model\Validators;

use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorResult;

/**
 * Class Validator.
 */
abstract class Validator
{
    /**
     * @var array $additionalOptions
     */
    protected $additionalOptions = [];

    /**
     * @param string $code
     *
     * @return PhpCodeValidatorResult
     */
    abstract public function checkCode($code);

    /**
     * Returns the key of the additionalOption matrix.
     * Each validator can have its own dynamic configuration.
     *
     * @return string
     */
    abstract protected function getAdditionalOptionsKey();

    /**
     * @param array $additionalOptions
     */
    final public function addAdditionalOptions($additionalOptions)
    {
        $key = $this->getAdditionalOptionsKey();

        if (array_key_exists($key, $additionalOptions)) {
            $this->additionalOptions =
                array_merge(
                    $this->additionalOptions,
                    $additionalOptions[$key]
                );
        }
    }
}
