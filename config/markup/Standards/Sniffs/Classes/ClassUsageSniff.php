<?php

declare(strict_types=1);

namespace Standards\Sniffs\Classes;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class ClassUsageSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $previousToken = $phpcsFile->getTokens()[$stackPtr - 1];

        if ($previousToken['code'] !== T_STRING) {
            $usageEndPtr = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR], start: $stackPtr + 1, exclude: true);
            $usageName = $phpcsFile->getTokensAsString($stackPtr, length: $usageEndPtr - $stackPtr);

            $phpcsFile->addError(
                error: 'Missing import for "%s" via use statement',
                stackPtr: $stackPtr,
                code: 'ClassUsage',
                data: [$usageName],
            );
        }
    }

    #[Override]
    public function register(): array
    {
        return [T_NS_SEPARATOR];
    }
}
