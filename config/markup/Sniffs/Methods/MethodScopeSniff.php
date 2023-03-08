<?php

declare(strict_types=1);

namespace App\Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class MethodScopeSniff implements Sniff
{
    private array $publicMethods = ['__construct'];

    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        $methodProperties = $phpcsFile->getMethodProperties($stackPtr);

        if (in_array($methodName, $this->publicMethods) && $methodProperties['scope'] !== 'public') {
            $phpcsFile->addError(
                error: 'Constructor must always have public visibility',
                stackPtr: $stackPtr,
                code: 'MethodScope',
            );
        }

        if ($methodProperties['is_static'] === true) {
            $phpcsFile->addError(
                error: 'Declaring static methods are forbidden',
                stackPtr: $stackPtr,
                code: 'MethodScope',
            );
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_FUNCTION];
    }
}
