<?php

namespace Trovit\PhpCodeValidator\Model\Validators;

use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorProblem;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Reporter;
use PHP_CodeSniffer\Runner;
use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorResult;

/**
 * Class CodeSnifferValidator
 *
 * @package Trovit\PhpCodeValidator\Model\Validators
 */
class CodeSnifferValidator extends Validator
{
    const ERROR_NAME = 'Syntax Error';

    /**
     * @var array
     */
    private $defaultSettings;

    /**
     * @var array
     */
    private $overrideSettings;

    /**
     * CodeSnifferValidatorTool constructor.
     *
     * @param array $settings
     */
    public function __construct(
        $settings
    )
    {
        $this->defaultSettings = $settings;
        $this->overrideSettings = [];
    }

    /**
     * @param string $code
     * @return PhpCodeValidatorResult
     */
    public function checkCode($code)
    {
        $runner = $this->buildRunner();
        $runner->init();
        $this->populateConfig($runner->config, $this->overrideSettings);
        $runner->reporter = new Reporter($runner->config);
        $file = new DummyFile($code, $runner->ruleset, $runner->config);
        $file->path = '/fakeFile.php';

        $runner->processFile($file);

        ob_start();
        $runner->reporter->printReports();
        $generatedJsonReport = ob_get_contents();
        ob_end_clean();

        return $this->buildErrorsFromJson(json_decode($generatedJsonReport));

    }

    /**
     * @return Runner
     */
    private function buildRunner()
    {
        $runner = new Runner();
        $config = $this->buildDefaultConfig();
        $runner->config = $config;
        return $runner;
    }

    /**
     * @return Config
     * @todo: wait for CS fix to remove $_SERVER['argv']
     */
    private function buildDefaultConfig()
    {
        $_SERVER['argv'] = [];
        $config = new Config();
        $this->populateConfig($config, $this->defaultSettings);
        return $config;
    }

    /**
     * @param array $overrideSettings
     */
    public function setOverrideSettings(array $overrideSettings)
    {
        $this->overrideSettings = $overrideSettings;
    }

    /**
     * @param \stdClass $generatedJsonReport
     * @return PhpCodeValidatorResult
     */
    private function buildErrorsFromJson($generatedJsonReport)
    {
        $result = new PhpCodeValidatorResult();
        if($generatedJsonReport->totals->errors || $generatedJsonReport->totals->warnings){
            $codeSnifferProblems = $generatedJsonReport->files->{'/fakeFile.php'}->messages;
            foreach ($codeSnifferProblems as $codeSnifferProblem){
                $errors[] = $result->addProblem(
                    $codeSnifferProblem->message,
                    self::ERROR_NAME,
                    $codeSnifferProblem->type === 'ERROR'
                        ? PhpCodeValidatorProblem::ERROR_TYPE
                        : PhpCodeValidatorProblem::WARNING_TYPE,
                    $codeSnifferProblem->line,
                    $codeSnifferProblem->column
                );
            }
        }
        return $result;
    }

    /**
     * @param Config $config
     * @param array $settings
     */
    private function populateConfig(Config $config, $settings)
    {
        foreach ($settings as $setting => $value){
            $config->$setting = $value;
        }
    }
}
