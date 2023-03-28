<?php

declare(strict_types=1);

namespace App\Sniffs\Annotations;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class AnnotationTagSniff implements Sniff
{
    public array $availableTags = ['deprecated', 'param', 'return', 'throws', 'var'];

    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $tagName = ltrim($tokens[$stackPtr]['content'], characters: '@');

        if (!in_array($tagName, $this->availableTags)) {
            $phpcsFile->addError(
                error: 'Annotation tag @%s is forbidden',
                stackPtr: $stackPtr,
                code: 'AnnotationTag',
                data: [$tagName],
            );
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_DOC_COMMENT_TAG];
    }
}
