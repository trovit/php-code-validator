<?php
namespace Trovit\PhpCodeValidator\Tests\Functional\Sniffs;

use Symfony\Component\Yaml\Yaml;
use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorProblem;
use Trovit\PhpCodeValidator\Model\Validators\CodeSnifferValidator;

/**
 * Class BaseSniffs
 * @package  Trovit\PhpCodeValidator\Tests\Model\Sniffs
 */
abstract class BaseSniffs extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CodeSnifferValidator
     */
    public $codeSnifferTool;

    /**
     * @return array of dataSets arrays with the following structure
     * @example
     * [
     *      <checkDescription> => [
     *          <codeString>,
     *          <PhpCodeValidatorProblemObject>
     *      ]
     * ]
     */
    abstract public function getDataSet();

    public function setUp()
    {
        $this->codeSnifferTool = new CodeSnifferValidator(
            $this->getConfigCodeSniffer()
        );
        $this->setSpecificSniff();

    }

    /**
     * @dataProvider additionProvider
     * @param string $code the code to test
     * @param PhpCodeValidatorProblem[] $expectedErrors array of PhpCodeValidatorProblem
     */
    public function testSniff($code, $expectedErrors)
    {
        $problems = $this->codeSnifferTool->checkCode($code);
        $this->assertEquals($expectedErrors, $problems->getProblems());
    }

    /**
     * Provides the dataSet to testSniff
     *
     * @return array
     */
    public function additionProvider()
    {
        return $this->getDataSet();
    }

    /**
     * @throws \Exception
     */
    protected function setSpecificSniff()
    {
        $path = explode('\\', get_class($this));
        $sniffName = array_pop($path);
        $this->codeSnifferTool->setOverrideSettings(
            [ 'sniffs' => ['..'.preg_replace('|SniffTest$|', '', $sniffName)] ]
        );
    }

    /**
     * @return array
     */
    private function getConfigCodeSniffer()
    {
        $config = Yaml::parse(file_get_contents(__DIR__.'/../../resources/config/codeSnifferConfig.yml'));
        $config['standards'][1] = sprintf($config['standards'][1], __DIR__.'/../..');
        return $config;
    }
}
