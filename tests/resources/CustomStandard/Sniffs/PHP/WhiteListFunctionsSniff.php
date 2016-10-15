<?php

use PHP_CodeSniffer\Files\File;

/**
 * Shows an error when a php function, which it's not in the ignore list, is used.
 */
class CustomStandard_Sniffs_PHP_WhiteListFunctionsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
    /**
     * A list of allowed PHP functions.
     *
     * @var string[]
     */
    public $allowedFunctions = array(
        'var_dump',
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        //Php functions is a string...
        return array(T_STRING);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned
     * @param int  $stackPtr  The position of the current token in
     *                        the stack passed in $tokens
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $tokenContent = $tokens[$stackPtr]['content'];

        $phpFunctions = get_defined_functions()['internal'];
        foreach ($phpFunctions as $phpFunctionName) {
            if ($phpFunctionName === $tokenContent && !in_array($tokenContent, $this->allowedFunctions, 'string')) {
                $this->addError($phpcsFile, $stackPtr, $phpFunctionName);
            }
        }
    }

    /**
     * Generates the error or warning for this sniff.
     *
     * @param File   $phpcsFile The file being scanned
     * @param int    $stackPtr  The position of the forbidden function
     *                          in the token array
     * @param string $function  The name of the forbidden function
     */
    protected function addError($phpcsFile, $stackPtr, $function)
    {
        $phpcsFile->addError('Function %s() is not allowed', $stackPtr, 'Not Allowed', array($function));
    }
}
