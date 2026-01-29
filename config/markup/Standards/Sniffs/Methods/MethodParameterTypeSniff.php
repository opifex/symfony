<?php

declare(strict_types=1);

namespace Standards\Sniffs\Methods;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class MethodParameterTypeSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $methodName = $phpcsFile->getDeclarationName($stackPtr) ?? 'closure';
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
        return [T_FUNCTION];
    }
}
