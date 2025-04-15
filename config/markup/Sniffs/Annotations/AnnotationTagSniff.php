<?php

declare(strict_types=1);

namespace Sniffs\Annotations;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class AnnotationTagSniff implements Sniff
{
    public array $allowedTags = ['@extends', '@implements', '@param', '@return', '@throws', '@var'];

    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $tagName = $phpcsFile->getTokens()[$stackPtr]['content'];

        if (!in_array($tagName, $this->allowedTags)) {
            $phpcsFile->addError(
                error: 'Annotation tag %s is not allowed. Allowed tags are: %s',
                stackPtr: $stackPtr,
                code: 'AnnotationTag',
                data: [$tagName, implode(separator: ', ', array: $this->allowedTags)],
            );
        }
    }

    #[Override]
    public function register(): array
    {
        return [T_DOC_COMMENT_TAG];
    }
}
