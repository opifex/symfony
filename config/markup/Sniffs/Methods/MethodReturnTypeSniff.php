<?php

declare(strict_types=1);

namespace Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class MethodReturnTypeSniff implements Sniff
{
    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $isFunction = $tokens[$stackPtr]['code'] === T_FUNCTION;
        $methodName = $isFunction ? $phpcsFile->getDeclarationName($stackPtr) : 'lambda';
        $isMethodMagic = mb_substr($methodName, 0, 2) === '__';
        $methodProperties = $phpcsFile->getMethodProperties($stackPtr);
        $methodReturnTypes = str_replace(search: 'null', replace: '', subject: $methodProperties['return_type']);
        $methodReturnTypes = trim($methodReturnTypes, characters: '?|');

        if (!$isMethodMagic && $methodReturnTypes === '') {
            $phpcsFile->addError(
                error: 'Return type for the method "%s" is not specified',
                stackPtr: $stackPtr,
                code: 'MethodReturnType',
                data: [$methodName],
            );
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_CLOSURE, T_FUNCTION];
    }
}
