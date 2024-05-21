<?php

declare(strict_types=1);

namespace Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class OperatorEqualitySniff implements Sniff
{
    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();

        $phpcsFile->addFixableError(
            error: 'Using not strict equality comparison is forbidden',
            stackPtr: $stackPtr,
            code: 'OperatorEquality',
        );

        if ($tokens[$stackPtr]['code'] === T_IS_EQUAL) {
            $phpcsFile->fixer->replaceToken($stackPtr, content: '===');
        } elseif ($tokens[$stackPtr]['code'] === T_IS_NOT_EQUAL) {
            $phpcsFile->fixer->replaceToken($stackPtr, content: '!==');
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_IS_EQUAL, T_IS_NOT_EQUAL];
    }
}
