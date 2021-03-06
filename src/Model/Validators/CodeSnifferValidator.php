<?php

namespace Trovit\PhpCodeValidator\Model\Validators;

use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorProblem;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Reporter;
use PHP_CodeSniffer\Runner;
use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorResult;

/**
 * Class CodeSnifferValidator.
 */
class CodeSnifferValidator extends Validator
{
    const ERROR_NAME = 'Code Sniffer Error';

    /**
     * @var array
     */
    private $defaultSettings;

    /**
     * CodeSnifferValidatorTool constructor.
     *
     * @param array $settings
     */
    public function __construct(
        $settings
    ) {
        $this->defaultSettings = $settings;
    }

    /**
     * @param string $code
     *
     * @return PhpCodeValidatorResult
     */
    public function checkCode($code)
    {
        $runner = $this->buildRunner();
        $runner->config = $this->buildDefaultConfig();
        $this->populateConfig($runner->config, $this->defaultSettings);
        $this->modifyStandardDefault($runner->config);
        $runner->init();
        $this->populateConfig($runner->config, $this->additionalOptions);
        $runner->reporter = $this->buildReporter($runner);
        $file = $this->buildDummyFile($code, $runner);
        $file->path = '/fake_file.php';

        $runner->processFile($file);

        ob_start();
        $runner->reporter->printReports();
        $generatedJsonReport = ob_get_contents();
        ob_end_clean();

        return $this->buildErrorsFromJson(json_decode($generatedJsonReport));
    }

    /**
     * @param \stdClass $generatedJsonReport
     *
     * @return PhpCodeValidatorResult
     */
    public function buildErrorsFromJson($generatedJsonReport)
    {
        $result = $this->getPhpCodeValidatorResult();

        if ($generatedJsonReport->totals->errors || $generatedJsonReport->totals->warnings) {
            $codeSnifferProblems = $generatedJsonReport->files->{'/fake_file.php'}->messages;
            foreach ($codeSnifferProblems as $codeSnifferProblem) {
                $result->addProblem(
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
     * @param array  $settings
     */
    public function populateConfig(Config $config, $settings)
    {
        foreach ($settings as $setting => $value) {
            $config->$setting = $value;
        }
    }

    /**
     * @param Config $config
     * @param array  $settings
     */
    private function modifyStandardDefault(Config $config)
    {
        if (array_key_exists('standards', $this->additionalOptions)) {
            $config->standards = $this->additionalOptions['standards'];
            unset($this->additionalOptions['standards']);
        }
    }

    /**
     * @return Runner
     */
    public function buildRunner()
    {
        return new Runner();
    }

    /**
     * @return Config
     * @todo: wait for CS fix to remove $_SERVER['argv']
     */
    public function buildDefaultConfig()
    {
        $_SERVER['argv'] = [];

        return new Config();
    }

    /**
     * @param $runner
     *
     * @return Reporter
     */
    public function buildReporter($runner)
    {
        return new Reporter($runner->config);
    }

    /**
     * @param $code
     * @param $runner
     *
     * @return DummyFile
     */
    public function buildDummyFile($code, $runner)
    {
        return new DummyFile($code, $runner->ruleset, $runner->config);
    }

    /**
     * @return PhpCodeValidatorResult
     */
    public function getPhpCodeValidatorResult()
    {
        return new PhpCodeValidatorResult();
    }

    /**
     * @return string
     */
    protected function getAdditionalOptionsKey()
    {
        return 'php_code_sniffer';
    }
}
