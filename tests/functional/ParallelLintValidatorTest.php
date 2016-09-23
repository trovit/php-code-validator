<?php
namespace Trovit\PhpCodeValidator\Tests\Functional;

use Trovit\PhpCodeValidator\Model\Validators\ParallelLintValidator;
use Trovit\TemporaryFilesystem\FileHandler;


/**
 * Class ParallelLintValidator
 * @package  Trovit\PhpCodeValidator\Tests\Model
 */
class ParallelLintValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParallelLintValidator
     */
    private $sut;

    /**
     * Sets up the required objects
     */
    protected function setUp()
    {
        $this->sut = new ParallelLintValidator(
            new FileHandler(__DIR__.'/../resources/')
        );
    }

    public function testMissingKeyInFormatAd()
    {
        $filePath = __DIR__ .'/../resources/PhpCodeFiles/SyntaxErrors/missingKey.txt';
        $PhpCodeValidatorProblems = $this->getErrorsFromFilePath($filePath);
        $this->assertCount(1, $PhpCodeValidatorProblems);
        $this->assertEquals(1, $PhpCodeValidatorProblems[0]->getErrorType());
        $this->assertEquals(19, $PhpCodeValidatorProblems[0]->getLineNum());
        $this->assertEquals(
            'Unexpected \'}\'',
            $PhpCodeValidatorProblems[0]->getMessage()
        );
    }

    public function testMissingSemiColonInAddCategoryParser()
    {
        $filePath = __DIR__ .'/../resources/PhpCodeFiles/SyntaxErrors/missingSemiColon.txt';
        $PhpCodeValidatorProblems = $this->getErrorsFromFilePath($filePath);

        $this->assertCount(1, $PhpCodeValidatorProblems);
        $this->assertEquals(1, $PhpCodeValidatorProblems[0]->getErrorType());
        $this->assertEquals(12, $PhpCodeValidatorProblems[0]->getLineNum());
        $this->assertEquals(
            'Unexpected \'$categories\' (T_VARIABLE)',
            $PhpCodeValidatorProblems[0]->getMessage()
        );
    }

    /**
     * @param $filePath
     * @return \Trovit\PhpCodeValidator\Entity\PhpCodeValidatorProblem[]
     */
    private function getErrorsFromFilePath($filePath)
    {
        return $this->sut->checkCode(file_get_contents($filePath))->getProblems();
    }
}
