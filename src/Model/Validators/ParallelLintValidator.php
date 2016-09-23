<?php

namespace Trovit\PhpCodeValidator\Model\Validators;

use JakubOnderka\PhpParallelLint\ParallelLint;
use JakubOnderka\PhpParallelLint\Process\PhpExecutable;
use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorResult;
use Trovit\TemporaryFilesystem\FileHandler;

/**
 * Class ParallelLintValidator
 *
 * @package Trovit\PhpCodeValidator\Model\Validators
 */
class ParallelLintValidator extends Validator
{
    const ERROR_NAME = 'Php Syntax Error';

    /**
     * @var FileHandler
     */
    protected $fileHandler;

    /**
     * PhpFormatterValidatorTool constructor.
     * @param FileHandler $fileHandler
     */
    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    /**
     * @param string $code
     * @return PhpCodeValidatorResult
     * @throws \Exception
     */
    public function checkCode($code)
    {
        $result = new PhpCodeValidatorResult();

        $parallelLint = new ParallelLint(PhpExecutable::getPhpExecutable('php'));

        $filePath = $this->fileHandler->createTemporaryFileFromString($code);

        $syntaxErrors = $parallelLint->lint([$filePath]);

        if ($syntaxErrors->hasSyntaxError()) {
            foreach ($syntaxErrors->getErrors() as $error) {
                $result->addError(
                    $error->getNormalizedMessage(),
                    self::ERROR_NAME,
                    $error->getLine()
                );
            }
        }

        $this->fileHandler->deleteTemporaryFile($filePath);

        return $result;
    }
}
