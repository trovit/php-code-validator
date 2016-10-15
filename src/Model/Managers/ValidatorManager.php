<?php

namespace Trovit\PhpCodeValidator\Model\Managers;

use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorResult;
use Trovit\PhpCodeValidator\Exception\BadClassProvidedException;
use Trovit\PhpCodeValidator\Model\Validators\Validator;

/**
 * Class ValidatorManager.
 *
 * This class execute all the validators in the config
 */
class ValidatorManager
{
    /**
     * @var Validator[]
     */
    private $validatorsClasses;

    /**
     * FormatterManager constructor.
     *
     * @param Validator[] $validatorsClasses
     */
    public function __construct(array $validatorsClasses)
    {
        $this->validatorsClasses = $validatorsClasses;
    }

    /**
     * Execute a group of strategies in lazy mode.
     *
     * @param string $code
     *
     * @return PhpCodeValidatorResult
     *
     * @throws BadClassProvidedException
     */
    public function execute($code)
    {
        $result = new PhpCodeValidatorResult();
        $max = count($this->validatorsClasses);
        for ($i = 0; $i < $max && !$result->hasProblems(); ++$i) {
            $validator = $this->validatorsClasses[$i];
            if (!$validator instanceof Validator) {
                throw new BadClassProvidedException('Class should be a formatter');
            }
            $result->addProblems(
                $validator->checkCode($code)->getProblems()
            );
        }

        return $result;
    }
}
