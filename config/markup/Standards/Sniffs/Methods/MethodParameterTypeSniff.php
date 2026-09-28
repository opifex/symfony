<?php

declare(strict_types=1);

namespace Standards\Sniffs\Methods;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class MethodParameterTypeSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $methodName = $this->getMethodName($phpcsFile, $stackPtr);
        $methodParameters = $phpcsFile->getMethodParameters($stackPtr);

        foreach ($methodParameters as $parameter) {
            if ($parameter['type_hint'] === '') {
                $phpcsFile->addError(
                    error: 'The method "%s" has parameter %s without type hinting',
                    stackPtr: $stackPtr,
                    code: 'MethodParameterType',
                    data: [$methodName, $parameter['name']],
                );
            }
        }
    }

    #[Override]
    public function register(): array
    {
        return [T_FUNCTION, T_FN, T_CLOSURE];
    }

    private function getMethodName(File $phpcsFile, mixed $stackPtr): string
    {
        return match ($phpcsFile->getTokens()[$stackPtr]['code']) {
            T_FN => 'arrow function',
            T_CLOSURE => 'closure',
            default => $phpcsFile->getDeclarationName($stackPtr) ?? 'closure',
        };
    }
}
