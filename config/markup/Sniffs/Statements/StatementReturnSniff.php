<?php

declare(strict_types=1);

namespace App\Sniffs\Statements;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class StatementReturnSniff implements Sniff
{
    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $scopeOpener = $tokens[$stackPtr]['scope_opener'] ?? 0;
        $scopeCloser = $tokens[$stackPtr]['scope_closer'] ?? 0;
        $returnStatementCount = 0;

        for ($stackPtr = $scopeOpener; $stackPtr <= $scopeCloser; $stackPtr++) {
            if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
                $stackPtr = $tokens[$stackPtr]['scope_closer'];
                continue;
            }

            if ($tokens[$stackPtr]['code'] === T_RETURN) {
                $returnStatementCount++;

                if ($returnStatementCount > 1) {
                    $phpcsFile->addError(
                        error: 'Only one return statement inside function is allowed',
                        stackPtr: $stackPtr,
                        code: 'StatementReturn',
                    );
                }
            }
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_FUNCTION];
    }
}
