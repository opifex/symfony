<?php

declare(strict_types=1);

namespace Sniffs\Statements;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class StatementElseSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $nextTokenPtr = $phpcsFile->findNext([T_WHITESPACE], start: $stackPtr + 1, exclude: true);
        $nextToken = $phpcsFile->getTokens()[$nextTokenPtr];

        if ($nextTokenPtr !== false && $nextToken['code'] !== T_IF) {
            $phpcsFile->addError(
                error: 'Usage of ELSE statement are basically not necessary',
                stackPtr: $stackPtr,
                code: 'StatementElse',
            );
        }
    }

    #[Override]
    public function register(): array
    {
        return [T_ELSE];
    }
}
