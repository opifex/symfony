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
        $tokens = $phpcsFile->getTokens();
        $currentToken = $tokens[$stackPtr];

        if ($currentToken['code'] === T_NS_SEPARATOR) {
            if ($tokens[$stackPtr - 1]['code'] === T_STRING) {
                return;
            }

            $usageEndPtr = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR], start: $stackPtr + 1, exclude: true);
            $usageName = $phpcsFile->getTokensAsString($stackPtr, length: $usageEndPtr - $stackPtr);
        } else {
            $usageName = $currentToken['content'];
        }

        $phpcsFile->addError(
            error: 'Missing import for "%s" via use statement',
            stackPtr: $stackPtr,
            code: 'ClassUsage',
            data: [$usageName],
        );
    }

    #[Override]
    public function register(): array
    {
        return [T_NS_SEPARATOR, T_NAME_FULLY_QUALIFIED, T_NAME_RELATIVE];
    }
}
