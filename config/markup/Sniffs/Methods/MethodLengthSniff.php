<?php

declare(strict_types=1);

namespace Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class MethodLengthSniff implements Sniff
{
    public int $lineLimit = 120;

    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $scopeOpener = $tokens[$stackPtr]['scope_opener'] ?? null;
        $scopeCloser = $tokens[$stackPtr]['scope_closer'] ?? null;
        $scopeFirstLine = $scopeOpener ? $tokens[$scopeOpener]['line'] : 0;
        $scopeLastLine = $scopeCloser ? $tokens[$scopeCloser]['line'] : 0;
        $scopeLength = $scopeLastLine - $scopeFirstLine;

        if ($scopeLength > $this->lineLimit) {
            $phpcsFile->addError(
                error: 'The method "%s" has %s lines of code, but current threshold is %s',
                stackPtr: $stackPtr,
                code: 'MethodLength',
                data: [$phpcsFile->getDeclarationName($stackPtr), $scopeLength, $this->lineLimit],
            );
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_FUNCTION];
    }
}
