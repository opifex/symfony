<?php

declare(strict_types=1);

namespace Sniffs\Operators;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class OperatorIncDecSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        $isPostfix = $tokens[($stackPtr - 1)]['code'] === T_VARIABLE;
        $isPrefix = $tokens[($stackPtr + 1)]['code'] === T_VARIABLE;

        if (!$isPostfix && ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE || $isPrefix)) {
            $phpcsFile->addError(
                error: 'Increment and decrement operators must have postfix format.',
                stackPtr: $stackPtr,
                code: 'OperatorIncDec',
            );
        }
    }

    #[Override]
    public function register(): array
    {
        return [T_DEC, T_INC];
    }
}
