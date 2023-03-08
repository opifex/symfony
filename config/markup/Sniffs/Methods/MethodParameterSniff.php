<?php

declare(strict_types=1);

namespace App\Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class MethodParameterSniff implements Sniff
{
    public array $allowedBoolPrefixes = ['set', 'with'];

    public int $parameterLimit = 10;

    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $isLambda = $tokens[$stackPtr]['code'] === T_CLOSURE;
        $methodName = $isLambda ? 'lambda' : $phpcsFile->getDeclarationName($stackPtr);
        $methodParams = $phpcsFile->getMethodParameters($stackPtr);
        $methodParamsCount = count($methodParams);
        preg_match(pattern: '/^[a-z]*/', subject: $methodName, matches: $matches);
        $methodNamePrefix = $matches[0] ?? '';

        if ($methodParamsCount > $this->parameterLimit) {
            $phpcsFile->addError(
                error: 'The method "%s" has %s parameters, but only %s is allowed',
                stackPtr: $stackPtr,
                code: 'MethodParameter',
                data: [$methodName, $methodParamsCount, $this->parameterLimit],
            );
        }

        foreach ($methodParams as $methodParameter) {
            if ($methodParameter['type_hint'] === '') {
                $phpcsFile->addError(
                    error: 'The method "%s" has parameter %s without type hinting',
                    stackPtr: $stackPtr,
                    code: 'MethodParameter',
                    data: [$methodName, $methodParameter['name']],
                );
            }

            if (!$isLambda && !in_array($methodNamePrefix, $this->allowedBoolPrefixes)) {
                if ($methodParameter['type_hint'] === 'bool') {
                    $phpcsFile->addError(
                        error: 'The method "%s" has boolean parameter %s which is a certain sign of a SRP violation',
                        stackPtr: $stackPtr,
                        code: 'MethodParameter',
                        data: [$methodName, $methodParameter['name']],
                    );
                }
            }
        }

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_CLOSURE, T_FUNCTION];
    }
}
