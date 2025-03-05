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
        $tokens = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]['content'];

        if (str_starts_with($content, '"') && str_ends_with($content, '"')) {
            if (!preg_match(pattern: '/[$\\\]/', subject: $content)) {
                $phpcsFile->addFixableError(
                    error: 'String must be enclosed in single quotes',
                    stackPtr: $stackPtr,
                    code: 'ConstantString',
                );
                $replaced = strtr(trim($content, characters: '"'), ['\'' => '\\\'', '\"' => '"']);
                $phpcsFile->fixer->replaceToken($stackPtr, content: '\'' . $replaced . '\'');
            }
        }
    }

    public function register(): array
    {
        return [T_CONSTANT_ENCAPSED_STRING];
    }
}
