<?php

declare(strict_types=1);

namespace App\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class CodeStructureSniff implements Sniff
{
    public string $appNamespace = 'App';

    public array $allowedNamespaces = [
        'App\\Application' => ['App\\Domain'],
        'App\\Domain' => [],
        'App\\Infrastructure' => ['App\\Domain'],
        'App\\Presentation' => ['App\\Application', 'App\\Domain'],
        'App\\Reports' => [],
        'App\\Sniffs' => [],
        'App\\Tests' => ['App\\Application', 'App\\Domain', 'App\\Infrastructure', 'App\\Presentation'],
    ];

    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $stackPtr = $phpcsFile->findNext([T_NAMESPACE], start: $stackPtr + 1);

        if ($stackPtr !== false) {
            $endPtr = $phpcsFile->findNext([T_SEMICOLON], start: $stackPtr + 1);
            $namespaceName = $phpcsFile->getTokensAsString(start: $stackPtr + 2, length: $endPtr - $stackPtr - 2);
            $namespacePrefix = $this->sliceNamespace($namespaceName);
            $availableImports = [...$this->allowedNamespaces[$namespacePrefix] ?? [], ...[$namespacePrefix]];
            $isNamespaceAllowed = in_array($namespacePrefix, array_keys($this->allowedNamespaces));

            if (!$isNamespaceAllowed && $namespaceName !== $this->appNamespace) {
                $phpcsFile->addError(
                    error: 'Namespace must starts with %s',
                    stackPtr: $stackPtr + 1,
                    code: 'CodeStructure',
                    data: [implode(separator: ' or ', array: array_keys($this->allowedNamespaces))],
                );
            }

            while ($stackPtr = $phpcsFile->findNext([T_USE], start: $stackPtr + 1)) {
                $stackPtr = $phpcsFile->findNext([T_WHITESPACE], start: $stackPtr + 1, exclude: true);
                $endPtr = $phpcsFile->findNext([T_SEMICOLON], start: $stackPtr + 1);
                $dependencyName = $phpcsFile->getTokensAsString($stackPtr, length: $endPtr - $stackPtr);

                if (mb_substr_count(haystack: $dependencyName, needle: '\\') === 0) {
                    continue;
                }

                if ($this->sliceNamespace($dependencyName, length: 1) !== $this->appNamespace) {
                    continue;
                }

                if (!in_array($this->sliceNamespace($dependencyName), $availableImports)) {
                    $phpcsFile->addError(
                        error: 'Usage must starts with %s',
                        stackPtr: $stackPtr + 1,
                        code: 'CodeStructure',
                        data: [implode(separator: ' or ', array: $availableImports)],
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
