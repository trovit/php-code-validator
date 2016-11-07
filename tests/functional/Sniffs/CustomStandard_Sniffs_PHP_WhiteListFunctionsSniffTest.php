<?php

namespace Trovit\PhpCodeValidator\Tests\Functional\Sniffs;

use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorProblem;
use Trovit\PhpCodeValidator\Model\Validators\CodeSnifferValidator;
use Trovit\PhpCodeValidator\Tests\BaseSniffs;

/**
 * Class CustomStandard_Sniffs_PHP_ForbiddenFunctionsSniffTest.
 */
class CustomStandard_Sniffs_PHP_WhiteListFunctionsSniffTest extends BaseSniffs
{
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
    public function getDataSet()
    {
        return [
            'Function base64_decode is not allowed' => [
                    '<?php $ad->setField(JobsAd::CONTACT_EMAIL, base64_decode(); ?>',
                    [
                        (new PhpCodeValidatorProblem())
                        ->setErrorName(CodeSnifferValidator::ERROR_NAME)
                        ->setMessage('Function base64_decode() is not allowed')
                        ->setColumnNum(44)
                        ->setErrorType(PhpCodeValidatorProblem::ERROR_TYPE)
                        ->setLineNum(1),
                    ],
                ],
                'Function var_dump is allowed' => [
                    '<?php $ad->setField(JobsAd::CONTACT_EMAIL, var_dump(\'mailto:\'); ?>',
                    [],
                ],
        ];
    }
}
