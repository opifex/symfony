<?php

declare(strict_types=1);

namespace Sniffs\Lists;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class TrailingCommaMultilineSniff implements Sniff
{
    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_CLOSE_SHORT_ARRAY) {
            $opener = $tokens[$stackPtr]['bracket_opener'];
            $closer = $tokens[$stackPtr]['bracket_closer'];
        } elseif ($tokens[$stackPtr]['code'] === T_MATCH) {
            $opener = $tokens[$stackPtr]['scope_opener'];
            $closer = $tokens[$stackPtr]['scope_closer'];
        } elseif ($tokens[$stackPtr]['code'] === T_OPEN_PARENTHESIS) {
            $opener = $tokens[$stackPtr]['parenthesis_opener'];
            $closer = $tokens[$stackPtr]['parenthesis_closer'];
        } elseif ($tokens[$stackPtr]['code'] === T_PARAM_NAME) {
            $opener = key($tokens[$stackPtr]['nested_parenthesis']);
            $closer = current($tokens[$stackPtr]['nested_parenthesis']);
        }

        if (isset($opener) && isset($closer)) {
            for ($index = $closer; $index >= $opener; $index--) {
                if ($tokens[$index]['code'] === T_WHITESPACE && $tokens[$index]['content'] === PHP_EOL) {
                    if ($tokens[$index - 1]['code'] !== T_COMMA) {
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

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_CLOSE_SHORT_ARRAY, T_MATCH, T_OPEN_PARENTHESIS];
    }
}
