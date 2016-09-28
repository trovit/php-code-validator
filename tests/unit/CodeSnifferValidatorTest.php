<?php
namespace Trovit\PhpCodeValidator\Tests\Unit;

use JakubOnderka\PhpParallelLint\SyntaxError;
use JakubOnderka\PhpParallelLint\ParallelLint;
use JakubOnderka\PhpParallelLint\Result;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Reporter;
use PHP_CodeSniffer\Runner;
use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorResult;
use Trovit\PhpCodeValidator\Model\Validators\CodeSnifferValidator;
use Trovit\PhpCodeValidator\Model\Validators\ParallelLintValidator;
use Trovit\TemporaryFilesystem\FileHandler;

/**
 * Class CodeSnifferValidatorTest
 * @package  Trovit\PhpCodeValidator\Tests\Model
 */
class CodeSnifferValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testCheckCode()
    {
        $parallelLintValidatorMock = $this->getCodeSnifferValidatorCheckCodeMock();
        $parallelLintValidatorMock->checkCode('code');
    }

    public function testBuildErrorsFromJson()
    {
        $parallelLintValidatorMock =
            $this->getCodeSnifferValidatorBuildErrorsFromJsonMock();
        $parallelLintValidatorMock->buildErrorsFromJson($this->getJsonErrorsMock());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getCodeSnifferValidatorCheckCodeMock()
    {
        $codeSnifferValidatorMock = $this
            ->getMockBuilder(CodeSnifferValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'populateConfig',
                    'buildRunner',
                    'buildDefaultConfig',
                    'buildReporter',
                    'buildDummyFile',
                    'buildErrorsFromJson'
                ]
            )
            ->getMock();

        $codeSnifferValidatorMock
            ->expects($this->once())
            ->method('buildRunner')
            ->willReturn($this->getRunnerMock());

        $codeSnifferValidatorMock
            ->expects($this->once())
            ->method('buildDefaultConfig')
            ->willReturn($this->getConfigMock());

        $codeSnifferValidatorMock
            ->expects($this->once())
            ->method('buildReporter')
            ->willReturn($this->getReporterMock());

        $codeSnifferValidatorMock
            ->expects($this->once())
            ->method('buildDummyFile')
            ->willReturn($this->getDummyFileMock());

        $codeSnifferValidatorMock
            ->expects($this->once())
            ->method('buildErrorsFromJson')
            ->willReturn($this->getReporterMock());

        $codeSnifferValidatorMock
            ->expects($this->exactly(2))
            ->method('populateConfig');

        return $codeSnifferValidatorMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRunnerMock()
    {
        $runnerMock = $this
            ->getMockBuilder(Runner::class)
            ->disableOriginalConstructor()
            ->getMock();

        $runnerMock
            ->expects($this->once())
            ->method('init');

        $runnerMock
            ->expects($this->once())
            ->method('processFile');

        return $runnerMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getConfigMock()
    {
        return $this
            ->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getReporterMock()
    {
        $reporterMock = $this
            ->getMockBuilder(Reporter::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $reporterMock;
    }

    private function getDummyFileMock()
    {
        return $this
            ->getMockBuilder(DummyFile::class)
            ->disableOriginalConstructor()
            ->getMock();
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getCodeSnifferValidatorBuildErrorsFromJsonMock()
    {
        $codeSnifferValidatorMock = $this
            ->getMockBuilder(CodeSnifferValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPhpCodeValidatorResult'])
            ->getMock();

        $codeSnifferValidatorMock
            ->expects($this->once())
            ->method('getPhpCodeValidatorResult')
            ->willReturn($this->getPhpCodeResultMock());

        return $codeSnifferValidatorMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getPhpCodeResultMock()
    {
        $phpCodeValidatorResult = $this
            ->getMockBuilder(PhpCodeValidatorResult::class)
            ->disableOriginalConstructor()
            ->getMock();

        $phpCodeValidatorResult
            ->expects($this->exactly(8))
            ->method('addProblem');

        return $phpCodeValidatorResult;
    }

    private function getJsonErrorsMock()
    {
        return json_decode(
            file_get_contents(__DIR__.'/../resources/JsonErrors/codeSniffersErrors.json')
        );
    }
}
