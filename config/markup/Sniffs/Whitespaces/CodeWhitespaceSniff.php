<?php

declare(strict_types=1);

namespace Sniffs\Whitespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class CodeWhitespaceSniff implements Sniff
{
    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $current = $tokens[$stackPtr] ?? null;
        $previous = $tokens[$stackPtr - 1] ?? null;
        $next = $tokens[$stackPtr + 1] ?? null;
        $open = [T_ATTRIBUTE, T_BOOLEAN_NOT, T_OPEN_CURLY_BRACKET, T_OPEN_PARENTHESIS, T_OPEN_SQUARE_BRACKET];
        $close = [T_ATTRIBUTE_END, T_CLOSE_CURLY_BRACKET, T_CLOSE_PARENTHESIS, T_CLOSE_SQUARE_BRACKET, T_SEMICOLON];

        $isSpaceLong = strlen($current['content']) > 1;
        $isSpaceInBegin = in_array(needle: $previous['code'] ?? null, haystack: $open);
        $isSpaceInEnd = in_array(needle: $next['code'] ?? null, haystack: $close);

        if ($current['content'] !== PHP_EOL && ($isSpaceLong || $isSpaceInBegin || $isSpaceInEnd)) {
            if ($previous['content'] !== PHP_EOL && $previous['code'] !== T_COMMENT && $next['content'] !== PHP_EOL) {
                $phpcsFile->addFixableError(
                    error: 'Extra whitespaces must be removed',
                    stackPtr: $stackPtr,
                    code: 'CodeWhitespace',
                );

                if ($isSpaceInBegin || $isSpaceInEnd) {
                    $whitespace = '';
                }

                $phpcsFile->fixer->replaceToken(stackPtr: $stackPtr, content: $whitespace ?? ' ');
            }
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_WHITESPACE];
    }
}
