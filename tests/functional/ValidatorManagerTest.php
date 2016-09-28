<?php
namespace Trovit\PhpCodeValidator\Tests\Functional;

use Symfony\Component\Yaml\Yaml;
use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorProblem;
use Trovit\PhpCodeValidator\Exception\BadClassProvidedException;
use Trovit\PhpCodeValidator\Model\Validators\CodeSnifferValidator;
use Trovit\PhpCodeValidator\Model\Managers\ValidatorManager;
use Trovit\PhpCodeValidator\Model\Validators\ParallelLintValidator;
use Trovit\TemporaryFilesystem\FileHandler;

/**
 * Class ValidatorManagerTest
 *
 * @package Kolekti\PhpCodeValidatorBundle\Tests\Model
 */
class ValidatorManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteManagerWithCodeSnifferErrors()
    {
        $sut = new ValidatorManager(
            [
                new CodeSnifferValidator($this->getCodeSnifferConfig()),
            ]
        );
        $code = file_get_contents(__DIR__ .
            '/../resources/PhpCodeFiles/WithCodeSnifferProblems'.
            '/notAllowedFunctionAndSyntaxError.txt');
        $problems = $sut->execute($code);
        $this->assertCount(8, $problems);
        $this->assertEquals(
            (new PhpCodeValidatorProblem())
                ->setMessage('Line indented incorrectly; expected 0 spaces, found 4')
                ->setErrorType(PhpCodeValidatorProblem::ERROR_TYPE)
                ->setLineNum(5)
                ->setColumnNum(5)
                ->setErrorName(CodeSnifferValidator::ERROR_NAME),
            $problems[0]
        );
        $this->assertEquals(
            (new PhpCodeValidatorProblem())
                ->setMessage('Function base64_decode() is not allowed')
                ->setErrorType(PhpCodeValidatorProblem::ERROR_TYPE)
                ->setLineNum(6)
                ->setColumnNum(9)
                ->setErrorName(CodeSnifferValidator::ERROR_NAME),
            $problems[1]
        );
        $this->assertEquals(
            (new PhpCodeValidatorProblem())
                ->setMessage(
                    'A file should declare new symbols (classes, functions, '.
                    'constants, etc.) and cause no other side effects, or it '.
                    'should execute logic with side effects, but should not do'.
                    ' both. The first symbol is defined on line 5 and the first'.
                    ' side effect is on line 3.'
                )
                ->setErrorType(PhpCodeValidatorProblem::WARNING_TYPE)
                ->setLineNum(1)
                ->setColumnNum(1)
                ->setErrorName(CodeSnifferValidator::ERROR_NAME),
            $problems[5]
        );
    }

    public function testExecuteManagerWithPhpLintErrors()
    {
        $sut = new ValidatorManager(
            [
                new ParallelLintValidator(new FileHandler(__DIR__.'/../resources/'))
            ]
        );
        $code = file_get_contents(__DIR__.'/../resources/PhpCodeFiles/WithPhpSyntaxErrors/missingKey.txt');
        $problems = $sut->execute($code);

        $this->assertCount(1, $problems);
        $this->assertEquals(
            (new PhpCodeValidatorProblem())
                ->setMessage('Unexpected end of file, expecting function (T_FUNCTION)')
                ->setErrorType(PhpCodeValidatorProblem::ERROR_TYPE)
                ->setLineNum(8)
                ->setErrorName(ParallelLintValidator::ERROR_NAME),
            $problems[0]
        );
    }

    public function testExecuteManagerWithoutValidators()
    {
        $sut = new ValidatorManager([]);
        $problems = $sut->execute('');
        $this->assertEmpty($problems);
    }

    private function getCodeSnifferConfig()
    {
        $config = Yaml::parse(file_get_contents(__DIR__.'/../resources/config/codeSnifferConfig.yml'));
        $config['standards'][1] = sprintf($config['standards'][1], __DIR__.'/../');
        return $config;
    }
}
