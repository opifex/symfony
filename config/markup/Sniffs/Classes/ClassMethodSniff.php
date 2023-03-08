<?php

declare(strict_types=1);

namespace App\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ClassMethodSniff implements Sniff
{
    public string $ignorePattern = '(^(__|set|get|is|has|with|supports))i';

    public int $methodLimit = 25;

    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $scopeType = ucfirst($tokens[$stackPtr]['content']);
        $methodsCount = 0;

        while ($stackPtr = $phpcsFile->findNext([T_FUNCTION], start: $stackPtr + 1)) {
            if (!preg_match(pattern: $this->ignorePattern, subject: $phpcsFile->getDeclarationName($stackPtr))) {
                $methodsCount++;
            }
        }

        if ($methodsCount > $this->methodLimit) {
            $phpcsFile->addError(
                error: '%s has %s methods, but only %s is allowed, excluded get and set methods',
                stackPtr: $stackPtr,
                code: 'ClassMethod',
                data: [$scopeType, $methodsCount, $this->methodLimit],
            );
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_CLASS, T_ENUM, T_INTERFACE, T_TRAIT];
    }
}
