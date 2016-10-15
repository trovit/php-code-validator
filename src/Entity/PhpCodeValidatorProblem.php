<?php

namespace Trovit\PhpCodeValidator\Entity;

/**
 * Class PhpCodeValidatorProblem.
 */
class PhpCodeValidatorProblem
{
    const ERROR_TYPE = 1;
    const WARNING_TYPE = 2;

    /**
     * @var string
     */
    private $message;

    /**
     * @var int self::ERROR_TYPE or self::WARNING_TYPE
     */
    private $errorType;

    /**
     * @var int number of the line
     */
    private $lineNum;

    /**
     * @var int number of the column
     */
    private $columnNum;

    /**
     * @var string
     */
    private $errorName;

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return PhpCodeValidatorProblem
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return int self::*_TYPE
     */
    public function getErrorType()
    {
        return $this->errorType;
    }

    /**
     * @param int $errorType self::*_TYPE
     *
     * @return PhpCodeValidatorProblem
     */
    public function setErrorType($errorType)
    {
        $this->errorType = $errorType;

        return $this;
    }

    /**
     * @return int
     */
    public function getLineNum()
    {
        return $this->lineNum;
    }

    /**
     * @param int $lineNum
     *
     * @return PhpCodeValidatorProblem
     */
    public function setLineNum($lineNum)
    {
        $this->lineNum = $lineNum;

        return $this;
    }

    /**
     * @return int
     */
    public function getColumnNum()
    {
        return $this->columnNum;
    }

    /**
     * @param int $columnNum
     *
     * @return PhpCodeValidatorProblem
     */
    public function setColumnNum($columnNum)
    {
        $this->columnNum = $columnNum;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorName()
    {
        return $this->errorName;
    }

    /**
     * @param string $errorName
     *
     * @return PhpCodeValidatorProblem
     */
    public function setErrorName($errorName)
    {
        $this->errorName = $errorName;

        return $this;
    }
}
