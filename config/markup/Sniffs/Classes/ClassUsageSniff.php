<?php

declare(strict_types=1);

namespace Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ClassUsageSniff implements Sniff
{
    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr - 1]) && $tokens[$stackPtr - 1]['code'] !== T_STRING) {
            $startPos = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR], start: $stackPtr + 1, exclude: true);
            $namespace = $phpcsFile->getTokensAsString($stackPtr, length: $startPos - $stackPtr);
            $phpcsFile->addError(
                error: 'Missing import for "%s" via use statement',
                stackPtr: $stackPtr,
                code: 'ClassUsage',
                data: [$namespace],
            );
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_NS_SEPARATOR];
    }
}
