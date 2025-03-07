<?php

declare(strict_types=1);

namespace Sniffs\Strings;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class SingleQuoteStringSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $constantContent = $phpcsFile->getTokens()[$stackPtr]['content'];

        if (str_starts_with($constantContent, '"') && str_ends_with($constantContent, '"')) {
            if (!preg_match(pattern: '/[$\\\]/', subject: $constantContent)) {
                $phpcsFile->addFixableError(
                    error: 'String must be enclosed in single quotes',
                    stackPtr: $stackPtr,
                    code: 'ConstantString',
                );
                $replacedContent = strtr(trim($constantContent, characters: '"'), ['\'' => '\\\'', '\"' => '"']);
                $phpcsFile->fixer->replaceToken($stackPtr, content: '\'' . $replacedContent . '\'');
            }
        }
    }

    #[Override]
    public function register(): array
    {
        return [T_CONSTANT_ENCAPSED_STRING];
    }
}
