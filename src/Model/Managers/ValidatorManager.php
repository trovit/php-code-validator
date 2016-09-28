<?php


namespace Trovit\PhpCodeValidator\Model\Managers;

use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorProblem;
use Trovit\PhpCodeValidator\Exception\BadClassProvidedException;
use Trovit\PhpCodeValidator\Model\Validators\Validator;

/**
 * Class ValidatorManager
 *
 * This class execute all the validators in the config
 *
 * @package Trovit\PhpCodeValidator\Model\Managers
 */
class ValidatorManager
{
    /**
     * @var Validator[]
     */
    private $validatorsClasses;

    /**
     * FormatterManager constructor.
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
     * @return PhpCodeValidatorProblem[]
     * @throws BadClassProvidedException
     */
    public function execute($code)
    {
        $problems = [];
        for ($i = 0, $max = count($this->validatorsClasses); $i < $max && !array_filter($problems); $i++) {
            $validator = $this->validatorsClasses[$i];
            if (!$validator instanceof Validator) {
                throw new BadClassProvidedException('Class should be a formatter');
            }
            $problems += $validator->checkCode($code)->getProblems();
        }

        return $problems;
    }
}
