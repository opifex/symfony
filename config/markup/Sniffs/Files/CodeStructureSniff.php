<?php

declare(strict_types=1);

namespace App\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class CodeStructureSniff implements Sniff
{
    public array $appNamespaces = ['App'];

    public array $domainNamespaces = ['App\\Domain'];

    public array $testsNamespaces = ['App\\Tests'];

    public array $internalNamespaces = [
        'App\\Reports',
        'App\\Sniffs',
    ];

    public array $mainNamespaces = [
        'App\\Application',
        'App\\Infrastructure',
        'App\\Presentation',
    ];

    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $stackPtr = $phpcsFile->findNext([T_NAMESPACE], start: $stackPtr + 1);

        if ($stackPtr !== false) {
            $endPtr = $phpcsFile->findNext([T_SEMICOLON], start: $stackPtr + 1);
            $namespaceName = $phpcsFile->getTokensAsString(start: $stackPtr + 2, length: $endPtr - $stackPtr - 2);
            $namespaceSlice = $this->sliceNamespace($namespaceName);
            $availableNamespaces = array_merge(
                $this->appNamespaces,
                $this->domainNamespaces,
                $this->internalNamespaces,
                $this->mainNamespaces,
                $this->testsNamespaces,
            );
            $availableImports = array_merge($this->domainNamespaces, [$namespaceSlice]);

            if (!in_array($namespaceSlice, $availableNamespaces)) {
                $phpcsFile->addError(
                    error: 'Namespace must starts with %s',
                    stackPtr: $stackPtr + 1,
                    code: 'CodeStructure',
                    data: [implode(' or ', array_merge($this->domainNamespaces, $this->mainNamespaces))],
                );
            }

            while ($stackPtr = $phpcsFile->findNext([T_USE], start: $stackPtr + 1)) {
                $stackPtr = $phpcsFile->findNext([T_WHITESPACE], start: $stackPtr + 1, exclude: true);
                $endPtr = $phpcsFile->findNext([T_SEMICOLON], start: $stackPtr + 1);
                $dependencyName = $phpcsFile->getTokensAsString($stackPtr, length: $endPtr - $stackPtr);

                if (in_array($namespaceSlice, $this->testsNamespaces)) {
                    continue;
                }

                if (mb_substr_count(haystack: $dependencyName, needle: '\\') === 0) {
                    continue;
                }

                if (!in_array($this->sliceNamespace($dependencyName, length: 1), $this->appNamespaces)) {
                    continue;
                }

                if (!in_array($this->sliceNamespace($dependencyName), $availableImports)) {
                    $phpcsFile->addError(
                        error: 'Usage must starts with %s',
                        stackPtr: $stackPtr + 1,
                        code: 'CodeStructure',
                        data: [implode(' or ', $availableImports)],
                    );
                }
            }
        }

        return $phpcsFile->numTokens + 1;
    }

    public function register(): array
    {
        return [T_OPEN_TAG];
    }

    private function sliceNamespace(string $namespace, int $length = 2): string
    {
        $namespaceParts = explode(separator: '\\', string: $namespace);
        $namespaceSlice = array_slice($namespaceParts, offset: 0, length: $length);

        return implode(separator: '\\', array: $namespaceSlice);
    }
}
