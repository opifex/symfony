<?php

declare(strict_types=1);

namespace Standards\Sniffs\Whitespaces;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class LineWhitespaceSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $emptyLineCount = 0;
        $lineNumbersList = [];

        foreach ($phpcsFile->getTokens() as $key => $token) {
            if ($token['code'] !== T_WHITESPACE) {
                $lineNumbersList[$token['line']] = false;
                $emptyLineCount = $token['code'] === T_OPEN_CURLY_BRACKET ? 1 : 0;

                continue;
            }

            if (!isset($lineNumbersList[$token['line']])) {
                $lineNumbersList[$token['line']] = true;
                $emptyLineCount++;
            }

            if ($emptyLineCount > 2) {
                $phpcsFile->addFixableError(
                    error: 'Extra empty line must be removed',
                    stackPtr: $key - 1,
                    code: 'LineWhitespace',
                );
                $phpcsFile->fixer->replaceToken(stackPtr: $key - 1, content: '');
            }
        }
    }

    #[Override]
    public function register(): array
    {
        return [T_OPEN_TAG];
    }
}
