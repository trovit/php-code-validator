<?php

namespace Trovit\PhpCodeValidator\Entity;

/**
 * Class PhpCodeValidatorResult.
 */
class PhpCodeValidatorResult
{
    /**
     * @var PhpCodeValidatorProblem[]
     */
    private $errors = [];

    /**
     * @var PhpCodeValidatorProblem[]
     */
    private $warnings = [];

    /**+
     * @param string   $message
     * @param string   $errorName
     * @param int      $errorType PhpCodeValidatorProblem::*_TYPE
     * @param int|null $lineNum
     * @param int|null $columnNum
     */
    public function addProblem(
        $message,
        $errorName,
        $errorType,
        $lineNum = null,
        $columnNum = null
    ) {
        if ($errorType === PhpCodeValidatorProblem::ERROR_TYPE) {
            $this->addError($message, $errorName, $lineNum, $columnNum);
        } elseif ($errorType === PhpCodeValidatorProblem::WARNING_TYPE) {
            $this->addWarning($message, $errorName, $lineNum, $columnNum);
        }
    }

    /**
     * Add a validator error.
     *
     * @param string   $message
     * @param string   $errorName
     * @param int|null $lineNum
     * @param int|null $columnNum
     */
    public function addError(
        $message,
        $errorName,
        $lineNum = null,
        $columnNum = null
    ) {
        $this->errors[] = (new PhpCodeValidatorProblem())
            ->setErrorName($errorName)
            ->setErrorType(PhpCodeValidatorProblem::ERROR_TYPE)
            ->setLineNum($lineNum)
            ->setMessage($message)
            ->setColumnNum($columnNum);
    }

    /**
     * Add a validator error.
     *
     * @param string   $message
     * @param string   $errorName
     * @param int|null $lineNum
     * @param int|null $columnNum
     */
    public function addWarning(
        $message,
        $errorName,
        $lineNum = null,
        $columnNum = null
    ) {
        $this->warnings[] = (new PhpCodeValidatorProblem())
            ->setErrorName($errorName)
            ->setErrorType(PhpCodeValidatorProblem::WARNING_TYPE)
            ->setLineNum($lineNum)
            ->setMessage($message)
            ->setColumnNum($columnNum);
    }

    /**
     * @return bool
     */
    public function hasWarnings()
    {
        return count($this->warnings) > 0;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * @return bool
     */
    public function hasProblems()
    {
        return $this->hasErrors() || $this->hasWarnings();
    }

    /**
     * @return PhpCodeValidatorProblem[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return PhpCodeValidatorProblem[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @return PhpCodeValidatorProblem[]
     */
    public function getProblems()
    {
        return array_merge($this->errors, $this->warnings);
    }

    /**
     * @param $problems PhpCodeValidatorProblem[]
     */
    public function addProblems($problems)
    {
        foreach ($problems as $problem) {
            $this->addProblem(
                $problem->getMessage(),
                $problem->getErrorName(),
                $problem->getErrorType(),
                $problem->getLineNum(),
                $problem->getColumnNum()
            );
        }
    }
}
