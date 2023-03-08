<?php

declare(strict_types=1);

namespace App\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ClassLengthSniff implements Sniff
{
    public int $lineLimit = 1000;

    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $classOpener = $tokens[$stackPtr]['scope_opener'] ?? null;
        $classCloser = $tokens[$stackPtr]['scope_closer'] ?? null;
        $classFirstLine = $classOpener ? $tokens[$classOpener]['line'] : 0;
        $classLastLine = $classCloser ? $tokens[$classCloser]['line'] : 0;
        $classLength = $classLastLine - $classFirstLine;

        if ($classLength > $this->lineLimit) {
            $scopeType = ucfirst($tokens[$stackPtr]['content']);
            $phpcsFile->addError(
                error: '%s "%s" have %s lines of code, but current threshold is %s',
                stackPtr: $stackPtr,
                code: 'ClassLength',
                data: [$scopeType, $phpcsFile->getDeclarationName($stackPtr), $classLength, $this->lineLimit],
            );
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_CLASS, T_ENUM, T_INTERFACE, T_TRAIT];
    }
}
