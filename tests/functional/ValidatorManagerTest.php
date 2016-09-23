<?php
namespace Trovit\PhpCodeValidator\Tests\Functional;

use Symfony\Component\Yaml\Yaml;
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
    public function testExecuteManager()
    {
        $sut = new ValidatorManager(
            [
                new CodeSnifferValidator($this->getCodeSnifferConfig()),
                new ParallelLintValidator(new FileHandler(__DIR__.'/../resources/'))
            ]
        );
        $code = file_get_contents(__DIR__.'/../resources/PhpCodeFiles/notAllowedFunctionAndSyntaxError.txt');
        $problems = $sut->execute($code);
        var_dump($problems);
        //$this->assertEquals($formattedCode, $expectedCode);
    }

    public function testFormatCodeWithoutValidators(){
        $sut = new ValidatorManager([]);
        $code = file_get_contents(__DIR__.'/../resources/PhpCodeFiles/badIndentationCode.txt');
        $problems = $sut->execute($code);
        $this->assertEquals($code, $problems);
    }

    public function testFormatCodeWithBadFormatterClass(){
        $sut = new ValidatorManager([new \stdClass]);
        $this->expectException(BadClassProvidedException::class);
        $sut->execute('');
    }

    private function getCodeSnifferConfig()
    {
        $config = Yaml::parse(file_get_contents(__DIR__.'/../resources/config/codeSnifferConfig.yml'));
        $config['standards'][1] = sprintf($config['standards'][1], __DIR__.'');
        return $config;
    }
}
