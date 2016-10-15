<?php

namespace Trovit\PhpCodeValidator\Tests\Functional;

use Symfony\Component\Yaml\Yaml;
use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorProblem;
use Trovit\PhpCodeValidator\Model\Validators\CodeSnifferValidator;
use Trovit\PhpCodeValidator\Model\Validators\ParallelLintValidator;

/**
 * Class CodeSnifferValidatorTest.
 */
class CodeSnifferValidatorTest extends \PHPUnit_Framework_TestCase
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
        $this->sut = new CodeSnifferValidator(
            $this->getCodeSnifferConfig()
        );
    }

    public function testMissingKeyInFormatAd()
    {
        $filePath = __DIR__.'/../resources/PhpCodeFiles/WithCodeSnifferProblems/notAllowedFunctionAndSyntaxError.txt';
        $PhpCodeValidatorProblems = $this->getErrorsFromFilePath($filePath);

        $this->assertCount(8, $PhpCodeValidatorProblems);

        $this->assertEquals(PhpCodeValidatorProblem::ERROR_TYPE, $PhpCodeValidatorProblems[0]->getErrorType());
        $this->assertEquals(5, $PhpCodeValidatorProblems[0]->getLineNum());
        $this->assertEquals(5, $PhpCodeValidatorProblems[0]->getColumnNum());
        $this->assertEquals(
            'Line indented incorrectly; expected 0 spaces, found 4',
            $PhpCodeValidatorProblems[0]->getMessage()
        );

        $this->assertEquals(PhpCodeValidatorProblem::ERROR_TYPE, $PhpCodeValidatorProblems[1]->getErrorType());
        $this->assertEquals(6, $PhpCodeValidatorProblems[1]->getLineNum());
        $this->assertEquals(9, $PhpCodeValidatorProblems[1]->getColumnNum());
        $this->assertEquals(
            'Function base64_decode() is not allowed',
            $PhpCodeValidatorProblems[1]->getMessage()
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

    private function getCodeSnifferConfig()
    {
        $config = Yaml::parse(file_get_contents(__DIR__.'/../resources/config/codeSnifferConfig.yml'));
        $config['standards'][1] = sprintf($config['standards'][1], __DIR__.'/../');

        return $config;
    }
}
