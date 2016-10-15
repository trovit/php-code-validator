<?php

namespace Trovit\PhpCodeValidator\Model\Validators;

use JakubOnderka\PhpParallelLint\ParallelLint;
use JakubOnderka\PhpParallelLint\Process\PhpExecutable;
use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorResult;
use Trovit\TemporaryFilesystem\FileHandler;

/**
 * Class ParallelLintValidator.
 */
class ParallelLintValidator extends Validator
{
    const ERROR_NAME = 'Parallel Lint Error';

    /**
     * @var FileHandler
     */
    protected $fileHandler;

    /**
     * PhpFormatterValidatorTool constructor.
     *
     * @param FileHandler $fileHandler
     */
    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    /**
     * @param string $code
     *
     * @return PhpCodeValidatorResult
     *
     * @throws \Exception
     */
    public function checkCode($code)
    {
        $result = $this->getPhpCodeValidatorResult();

        $filePath = $this->fileHandler->createTemporaryFileFromString($code);

        $syntaxErrors = $this->getParallelLint()->lint([$filePath]);

        if ($syntaxErrors->hasSyntaxError()) {
            foreach ($syntaxErrors->getErrors() as $syntaxError) {
                $result->addError(
                    $syntaxError->getNormalizedMessage(),
                    self::ERROR_NAME,
                    $syntaxError->getLine()
                );
            }
        }

        $this->fileHandler->deleteTemporaryFile($filePath);

        return $result;
    }

    /**
     * @return ParallelLint
     */
    public function getParallelLint()
    {
        return new ParallelLint(PhpExecutable::getPhpExecutable('php'));
    }

    /**
     * @return PhpCodeValidatorResult
     */
    public function getPhpCodeValidatorResult()
    {
        return new PhpCodeValidatorResult();
    }
}
