<?php

declare(strict_types=1);

namespace App\Sniffs\Variables;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class VariableNameSniff implements Sniff
{
    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $variableName = ltrim($tokens[$stackPtr]['content'], characters: '$');

        if (!preg_match(pattern: '/^[a-z][a-zA-Z\d]*$/', subject: $variableName)) {
            $phpcsFile->addError(
                error: 'Variable $%s is not in valid camel caps format',
                stackPtr: $stackPtr,
                code: 'VariableName',
                data: [$variableName],
            );
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_VARIABLE];
    }
}
