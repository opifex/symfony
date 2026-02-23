<?php

declare(strict_types=1);

namespace Standards\Sniffs\Whitespaces;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class CodeWhitespaceSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $currentToken = $tokens[$stackPtr] ?? null;
        $previousToken = $tokens[$stackPtr - 1] ?? null;
        $nextToken = $tokens[$stackPtr + 1] ?? null;
        $openTags = [T_ATTRIBUTE, T_BOOLEAN_NOT, T_OPEN_CURLY_BRACKET, T_OPEN_PARENTHESIS, T_OPEN_SQUARE_BRACKET];
        $closeTags = [T_ATTRIBUTE_END, T_CLOSE_CURLY_BRACKET, T_CLOSE_PARENTHESIS, T_CLOSE_SQUARE_BRACKET, T_SEMICOLON];

        $spaceIsLong = strlen($currentToken['content']) > 1;
        $spaceIsComment = ($previousToken['code'] ?? null) === T_COMMENT;
        $spaceInBegin = in_array(needle: $previousToken['code'] ?? null, haystack: $openTags);
        $spaceInEnd = in_array(needle: $nextToken['code'] ?? null, haystack: $closeTags);

        if ($currentToken['content'] !== PHP_EOL && ($spaceIsLong || $spaceInBegin || $spaceInEnd)) {
            if ($previousToken['content'] !== PHP_EOL && !$spaceIsComment && $nextToken['content'] !== PHP_EOL) {
                $phpcsFile->addFixableError(
                    error: 'Extra whitespaces must be removed',
                    stackPtr: $stackPtr,
                    code: 'CodeWhitespace',
                );

                if ($spaceInBegin || $spaceInEnd) {
                    $whitespace = '';
                }

                $phpcsFile->fixer->replaceToken(stackPtr: $stackPtr, content: $whitespace ?? ' ');
            }
        }
    }

    #[Override]
    public function register(): array
    {
        return [T_WHITESPACE];
    }
}
