<?php

namespace Trovit\PhpCodeValidator\Tests\Functional\Sniffs;

use Symfony\Component\Yaml\Yaml;
use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorProblem;
use Trovit\PhpCodeValidator\Model\Validators\CodeSnifferValidator;

/**
 * Class BaseSniffs.
 */
abstract class BaseSniffs extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CodeSnifferValidator
     */
    public $codeSnifferTool;

    /**
     * @return array of dataSets arrays with the following structure
     *
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
     *
     * @param string $code the code to test
     * @param \Trovit\PhpCodeValidator\Entity\PhpCodeValidatorProblem[] $expectedProblems
     */
    public function testSniff($code, $expectedProblems)
    {
        $result = $this->codeSnifferTool->checkCode($code);
        $problems = $result->getProblems();
        $this->assertEquals($expectedProblems, $problems);
    }

    /**
     * Provides the dataSet to testSniff.
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
        $this->codeSnifferTool->addAdditionalOptions(
            [
                'php_code_sniffer' =>
                    [
                        'sniffs' => ['..'.preg_replace('|SniffTest$|', '', $sniffName)]
                    ]
            ]
        );
    }

    /**
     * @return array
     */
    protected function getConfigCodeSniffer()
    {
        $config = Yaml::parse(file_get_contents(__DIR__.'/../../resources/config/codeSnifferConfig.yml'));
        $config['standards'][1] = sprintf($config['standards'][1], __DIR__.'/../..');

        return $config;
    }
}
