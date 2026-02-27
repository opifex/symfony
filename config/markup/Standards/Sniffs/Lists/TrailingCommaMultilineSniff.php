<?php

declare(strict_types=1);

namespace Standards\Sniffs\Lists;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class TrailingCommaMultilineSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $currentToken = $tokens[$stackPtr];

        if ($currentToken['code'] === T_CLOSE_SHORT_ARRAY) {
            $scopeOpener = $currentToken['bracket_opener'];
            $scopeCloser = $currentToken['bracket_closer'];
        } elseif ($currentToken['code'] === T_MATCH) {
            $scopeOpener = $currentToken['scope_opener'];
            $scopeCloser = $currentToken['scope_closer'];
        } elseif ($currentToken['code'] === T_CLOSE_PARENTHESIS) {
            $scopeOpener = $currentToken['parenthesis_opener'];
            $scopeCloser = $currentToken['parenthesis_closer'];
        }

        if (isset($scopeOpener) && isset($scopeCloser)) {
            for ($index = $scopeCloser; $index >= $scopeOpener; $index--) {
                if ($tokens[$index]['code'] === T_WHITESPACE && $tokens[$index]['content'] === PHP_EOL) {
                    if (!in_array(needle: $tokens[$index - 1]['code'], haystack: [T_COMMA, T_SEMICOLON])) {
                        $phpcsFile->addFixableError(
                            error: 'Multi-line arrays, arguments and match expressions must have a trailing comma',
                            stackPtr: $index - 1,
                            code: 'TrailingCommaMultiline',
                        );
                        $phpcsFile->fixer->addContent(stackPtr: $index - 1, content: ',');
                    }

                    break;
                }
            }
        }
    }

    #[Override]
    public function register(): array
    {
        return [T_CLOSE_SHORT_ARRAY, T_MATCH, T_CLOSE_PARENTHESIS];
    }
}
