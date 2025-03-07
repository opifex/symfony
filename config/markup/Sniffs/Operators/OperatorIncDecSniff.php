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
        $previousToken = $phpcsFile->getTokens()[($stackPtr - 1)];
        $nextToken = $phpcsFile->getTokens()[($stackPtr + 1)];

        $operatorIsPostfix = $previousToken['code'] === T_VARIABLE;
        $operatorIsPrefix = $nextToken['code'] === T_VARIABLE;

        if (!$operatorIsPostfix && ($nextToken['code'] === T_WHITESPACE || $operatorIsPrefix)) {
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
