<?php
namespace Trovit\PhpCodeValidator\Tests\Functional\Sniffs;

use Trovit\PhpCodeValidator\Entity\PhpCodeValidatorProblem;

/**
 * Class CustomStandard_Sniffs_PHP_ForbiddenFunctionsSniffTest
 *
 * @package  Trovit\PhpCodeValidator\Tests\Model\Sniffs
 */
class CustomStandard_Sniffs_PHP_WhiteListFunctionsSniffTest extends BaseSniffs
{
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
    public function getDataSet()
    {
        return [
            'Function base64_decode is not allowed' =>
                [
                    '<?php $ad->setField(JobsAd::CONTACT_EMAIL, base64_decode(); ?>',
                    [
                        (new PhpCodeValidatorProblem())
                        ->setErrorName('Syntax Error')
                        ->setMessage('Function base64_decode() is not allowed')
                        ->setColumnNum(44)
                        ->setErrorType(PhpCodeValidatorProblem::ERROR_TYPE)
                        ->setLineNum(1)
                    ],
                ],
            'Function var_dump is allowed'          =>
                [
                    '<?php $ad->setField(JobsAd::CONTACT_EMAIL, var_dump(\'mailto:\'); ?>',
                    [],
                ],
        ];
    }
}
