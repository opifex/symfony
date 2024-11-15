<?php

declare(strict_types=1);

namespace Standards\Sniffs\Constants;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ConstantStringSniff implements Sniff
{
    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]['content'];

        if (mb_substr($content, 0, 1) === '"') {
            $phpcsFile->addFixableError(
                error: 'String must be enclosed in single quotes',
                stackPtr: $stackPtr,
                code: 'ConstantString',
            );
            $replaced = strtr(trim($content, characters: '"'), ['\'' => '\\\'', '\"' => '"']);
            $phpcsFile->fixer->replaceToken($stackPtr, content: '\'' . $replaced . '\'');
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_CONSTANT_ENCAPSED_STRING];
    }
}
