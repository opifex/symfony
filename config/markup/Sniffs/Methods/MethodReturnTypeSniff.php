<?php

declare(strict_types=1);

namespace Sniffs\Methods;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class MethodReturnTypeSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $methodProperties = $phpcsFile->getMethodProperties($stackPtr);
        $methodReturnType = strval($methodProperties['return_type']);

        $methodName = $phpcsFile->getDeclarationName($stackPtr) ?? 'closure';
        $isMethodMagic = mb_substr($methodName, 0, 2) === '__';

        if (!$isMethodMagic && $methodReturnType === '') {
            $phpcsFile->addError(
                error: 'Return type for the method "%s" is not specified',
                stackPtr: $stackPtr,
                code: 'MethodReturnType',
                data: [$methodName],
            );
        }
    }

    #[Override]
    public function register(): array
    {
        return [T_CLOSURE, T_FUNCTION];
    }
}
