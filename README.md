# Php Code Validator  

[![Build Status](https://travis-ci.org/trovit/php-code-validator.svg?branch=master)](https://travis-ci.org/trovit/php-code-validator)  

Provides a basic system to organize and execute php code validators.

There's a [Symfony bundle](https://github.com/trovit/php-code-validator-bundle) with this component.

## Code structure

![code strcutre](http://i.imgur.com/RZ6qmZ3.png)

## Create your own Validator

When you need to validate or check your code and the validators provided by this bundle doesn't satisfy your needs (different code language, formats, etc...) there is the possibility to create a new Validator class by implementing the Validator interface (_Trovit\PhpCodeValidator\Validators\Validator_) and implement its method *checkCode*

## List of available validators

- *CodeSnifferValidator*: Wrapper of [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)
- *ParallelLintValidator*: Wrapper of [PHP Parallel Lint](https://github.com/JakubOnderka/PHP-Parallel-Lint)
