<?php

declare(strict_types=1);

namespace Standards\Sniffs\Methods;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class MethodReturnTypeSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $currentToken = $phpcsFile->getTokens()[$stackPtr];
        $methodName = $phpcsFile->getDeclarationName($stackPtr) ?? 'closure';
        $methodProperties = $phpcsFile->getMethodProperties($stackPtr);
        $methodReturnType = strval($methodProperties['return_type']);
        $methodScopeOpener = $currentToken['scope_opener'] ?? 0;
        $methodScopeCloser = $currentToken['scope_closer'] ?? 0;
        $methodHasReturn = boolval($phpcsFile->findNext([T_RETURN], $methodScopeOpener, $methodScopeCloser));
        $methodIsMagic = str_starts_with($methodName, '__');

        if ($methodReturnType === '' && (!$methodIsMagic || $methodHasReturn)) {
            $phpcsFile->addError(
                error: 'Return type for the method "%s" is not specified',
                stackPtr: $stackPtr,
                code: 'MethodReturnType',
                data: [$methodName],
            );
        }
    }

    #[Override]
    public function register(): array
    {
        return [T_FUNCTION];
    }
}
