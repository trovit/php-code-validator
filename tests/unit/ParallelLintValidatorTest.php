<?php
namespace Trovit\PhpCodeValidator\Tests\Unit;

use JakubOnderka\PhpParallelLint\SyntaxError;
use JakubOnderka\PhpParallelLint\ParallelLint;
use JakubOnderka\PhpParallelLint\Result;
use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorResult;
use Trovit\PhpCodeValidator\Model\Validators\ParallelLintValidator;
use Trovit\TemporaryFilesystem\FileHandler;

/**
 * Class ParallelLintValidator
 * @package  Trovit\PhpCodeValidator\Tests\Model
 */
class ParallelLintValidatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getDataProvider
     */
    public function testCheckCode($numErrors)
    {
        $parallelLintValidatorMock = $this->getParallelLintValidatorMock($numErrors);
        $parallelLintValidatorMock->checkCode('code');
    }


    /**
     * Data provider for testCheckCode
     * @return array
     */
    public function getDataProvider()
    {
        return [[0,1,2]];
    }

    public function testCheckCodeWithBadFile()
    {
        $fileHandlerMock = $this->getMockBuilder(FileHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileHandlerMock
            ->expects($this->once())
            ->method('createTemporaryFileFromString');

        $this->expectException(\InvalidArgumentException::class);

        (new ParallelLintValidator($fileHandlerMock))->checkCode('code');
    }

    /**
     * @param $numErrors
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getParallelLintValidatorMock($numErrors)
    {
        $parallelLintValidatorMock = $this
            ->getMockBuilder(ParallelLintValidator::class)
            ->setMethods(
                [
                    '__construct',
                    'getParallelLint',
                    'getPhpCodeValidatorResult'
                ]
            )
            ->setConstructorArgs([$this->getFileHandlerMock()])
            ->getMock();

        $parallelLintMock = $this
            ->getMockBuilder(ParallelLint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $parallelLintValidatorMock
            ->expects($this->once())
            ->method('getPhpCodeValidatorResult')
            ->willReturn($this->getPhpCodeResultMock($numErrors));

        $parallelLintValidatorMock
            ->expects($this->once())
            ->method('getParallelLint')
            ->willReturn($parallelLintMock);

        $parallelLintMock
            ->expects($this->once())
            ->method('lint')
            ->willReturn($this->getParallelLintResultMock($numErrors));

        return $parallelLintValidatorMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFileHandlerMock()
    {
        $fileHandlerMock = $this
            ->getMockBuilder(FileHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileHandlerMock
            ->expects($this->once())
            ->method('createTemporaryFileFromString');

        $fileHandlerMock
            ->expects($this->once())
            ->method('deleteTemporaryFile');

        return $fileHandlerMock;
    }

    /**
     * @param int $numErrors
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getParallelLintResultMock($numErrors = 0)
    {
        $hasErrors = (bool) $numErrors;

        $parallelLintResultMock = $this
            ->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->getMock();

        $parallelLintResultMock
            ->expects($this->once())
            ->method('hasSyntaxError')
            ->willReturn($hasErrors);

        if ($hasErrors) {
            $parallelLintResultMock
                ->expects($this->once())
                ->method('getErrors')
                ->willReturn(
                    array_fill(
                        0,
                        $numErrors,
                        $this->getPhpParallelLitErrorMock($numErrors)
                    )
                );
        }

        return $parallelLintResultMock;
    }

    /**
     * @param $numErrors
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getPhpParallelLitErrorMock($numErrors)
    {
        $phpParallelLintError = $this
            ->getMockBuilder(SyntaxError::class)
            ->disableOriginalConstructor()
            ->getMock();

        $phpParallelLintError
            ->expects($this->exactly($numErrors))
            ->method('getNormalizedMessage');

        $phpParallelLintError
            ->expects($this->exactly($numErrors))
            ->method('getLine');

        return $phpParallelLintError;
    }

    /**
     * @param $numErrors
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getPhpCodeResultMock($numErrors)
    {
        $phpCodeValidatorResult = $this
            ->getMockBuilder(PhpCodeValidatorResult::class)
            ->disableOriginalConstructor()
            ->getMock();

        $phpCodeValidatorResult
            ->expects($this->exactly($numErrors))
            ->method('addError');

        return $phpCodeValidatorResult;
    }
}
