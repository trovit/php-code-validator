<?php
namespace Trovit\PhpCodeValidator\Tests\Unit;

use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorResult;
use Trovit\PhpCodeValidator\Exception\BadClassProvidedException;
use Trovit\PhpCodeValidator\Model\Managers\ValidatorManager;
use Trovit\PhpCodeValidator\Model\Validators\CodeSnifferValidator;
use Trovit\PhpCodeValidator\Model\Validators\ParallelLintValidator;

/**
 * Class ValidatorManagerTest
 *
 * @package Kolekti\PhpCodeValidatorBundle\Tests\Model
 */
class ValidatorManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteManagerWithCodeSnifferErrors()
    {
        $codeSnifferValidatorMock = $this->getCodeSnifferValidatorMock();
        $parallelLintValidator = $this->getParallelLintValidatorMock();

        $codeSnifferValidatorResult = new PhpCodeValidatorResult();
        $codeSnifferValidatorResult->addError(
            'Test code sniffer error',
            'codeSniffer'
        );

        $codeSnifferValidatorMock
            ->expects($this->once())
            ->method('checkCode')
            ->willReturn($codeSnifferValidatorResult);

        $parallelLintValidator
            ->expects($this->never())
            ->method('checkCode');

        $result = (new ValidatorManager(
            [$codeSnifferValidatorMock, $parallelLintValidator]
        ))->execute('code');

        static::assertEquals(
            $codeSnifferValidatorResult->getProblems(),
            $result->getProblems()
        );
    }

    public function testExecuteManagerWithBadValidatorClass()
    {
        $sut = new ValidatorManager([new \stdClass]);
        $this->expectException(BadClassProvidedException::class);
        $sut->execute('');
    }

    private function getCodeSnifferValidatorMock()
    {
        return $this->getMockBuilder(CodeSnifferValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getParallelLintValidatorMock()
    {
        return $this->getMockBuilder(ParallelLintValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
