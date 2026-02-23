<?php

declare(strict_types=1);

namespace Standards\Sniffs\Operators;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class OperatorEqualitySniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $currentToken = $phpcsFile->getTokens()[$stackPtr];
        $phpcsFile->addFixableError(
            error: 'Using not strict equality comparison is forbidden',
            stackPtr: $stackPtr,
            code: 'OperatorEquality',
        );

        if ($currentToken['code'] === T_IS_EQUAL) {
            $phpcsFile->fixer->replaceToken($stackPtr, content: '===');
        } elseif ($currentToken['code'] === T_IS_NOT_EQUAL) {
            $phpcsFile->fixer->replaceToken($stackPtr, content: '!==');
        }
    }

    #[Override]
    public function register(): array
    {
        return [T_IS_EQUAL, T_IS_NOT_EQUAL];
    }
}
