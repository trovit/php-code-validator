<?php

namespace Trovit\PhpCodeValidator\Tests\Functional;

use Trovit\PhpCodeValidator\Model\Validators\ParallelLintValidator;
use Trovit\TemporaryFilesystem\FileHandler;

/**
 * Class ParallelLintValidator.
 */
class ParallelLintValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParallelLintValidator
     */
    private $sut;

    /**
     * Sets up the required objects.
     */
    protected function setUp()
    {
        $this->sut = new ParallelLintValidator(
            new FileHandler(__DIR__.'/../resources/')
        );
    }

    public function testMissingKeyInFormatAd()
    {
        $filePath = __DIR__.'/../resources/PhpCodeFiles/WithPhpSyntaxErrors/missingKey.txt';
        $PhpCodeValidatorProblems = $this->getErrorsFromFilePath($filePath);
        $this->assertCount(1, $PhpCodeValidatorProblems);
        $this->assertEquals(1, $PhpCodeValidatorProblems[0]->getErrorType());
        $this->assertEquals(8, $PhpCodeValidatorProblems[0]->getLineNum());
        $this->assertEquals(
            'Unexpected end of file, expecting function (T_FUNCTION)',
            $PhpCodeValidatorProblems[0]->getMessage()
        );
    }

    public function testMissingSemiColonInAddCategoryParser()
    {
        $filePath = __DIR__.'/../resources/PhpCodeFiles/WithPhpSyntaxErrors/missingSemiColon.txt';
        $PhpCodeValidatorProblems = $this->getErrorsFromFilePath($filePath);

        $this->assertCount(1, $PhpCodeValidatorProblems);
        $this->assertEquals(1, $PhpCodeValidatorProblems[0]->getErrorType());
        $this->assertEquals(9, $PhpCodeValidatorProblems[0]->getLineNum());
        $this->assertEquals(
            'Unexpected \'}\'',
            $PhpCodeValidatorProblems[0]->getMessage()
        );
    }

    /**
     * @param $filePath
     *
     * @return \Trovit\PhpCodeValidator\Entity\PhpCodeValidatorProblem[]
     */
    private function getErrorsFromFilePath($filePath)
    {
        return $this->sut->checkCode(file_get_contents($filePath))->getProblems();
    }
}
