<?php

declare(strict_types=1);

namespace App\Sniffs\Statements;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class StatementUseSniff implements Sniff
{
    public int $dependencyLimit = 20;

    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $usedDependencies = $declaredDependencies = $stackPointers = [];
        $lastPtr = $phpcsFile->findNext([T_CLASS, T_ENUM, T_INTERFACE, T_TRAIT], start: $stackPtr + 1);
        $lastDependencyPtr = $stackPtr;

        while ($stackPtr = $phpcsFile->findNext([T_USE], start: $stackPtr + 1)) {
            if ($stackPtr > $lastPtr) {
                break;
            }

            $startPos = $phpcsFile->findNext([T_WHITESPACE], start: $stackPtr + 1, exclude: true);
            $endPos = $phpcsFile->findNext([T_SEMICOLON], start: $startPos + 1);
            $dependencyName = $phpcsFile->getTokensAsString($startPos, length: $endPos - $startPos);
            $lastDependencyPtr = $endPos;

            $aliasPosition = strrpos($dependencyName, needle: '\\');
            $dependencyAlias = substr($dependencyName, offset: $aliasPosition ? $aliasPosition + 1 : 0);
            $dependencyAlias = substr($dependencyAlias, offset: strpos($dependencyAlias, needle: ' as ') ?: 0);

            $dependencyParts = explode(separator: ' as ', string: $dependencyName);
            [$usage, $alias] = array_pad($dependencyParts, length: 2, value: $dependencyAlias);

            if (mb_substr_count(haystack: $usage, needle: ' ') === 0) {
                $usedDependencies[] = $dependencyName;
                $declaredDependencies[$alias] = $usage;
                $stackPointers[$alias] = $startPos;
            }
        }

        $sortedDependencies = $declaredDependencies;

        usort(array: $usedDependencies, callback: fn(string $strA, string $strB) => strcasecmp($strA, $strB));
        usort(array: $sortedDependencies, callback: fn(string $strA, string $strB) => strcasecmp($strA, $strB));

        if (array_values($sortedDependencies) !== array_values($declaredDependencies)) {
            $phpcsFile->addFixableError(
                error: 'File dependencies must be sorted alphabetically',
                stackPtr: current($stackPointers),
                code: 'StatementUse',
            );

            $index = 0;

            $phpcsFile->fixer->beginChangeset();

            foreach ($stackPointers as $stackPointer) {
                $endPos = $phpcsFile->findNext([T_SEMICOLON], start: $stackPointer);
                $phpcsFile->fixer->replaceToken($stackPointer, content: $usedDependencies[$index++]);

                for ($i = $stackPointer + 1; $i < $endPos; $i++) {
                    $phpcsFile->fixer->replaceToken($i, content: '');
                }
            }

            $phpcsFile->fixer->endChangeset();
        }

        $dependenciesCount = count($declaredDependencies);

        if ($dependenciesCount > $this->dependencyLimit) {
            $phpcsFile->addError(
                error: 'File have %s dependencies, but only %s is allowed',
                stackPtr: $stackPtr,
                code: 'StatementUse',
                data: [$dependenciesCount, $this->dependencyLimit],
            );
        }

        $usedDependencies = [];
        $startPos = $lastDependencyPtr;
        $searchKeys = [T_STRING, T_DOC_COMMENT_STRING, T_DOC_COMMENT_WHITESPACE];

        while ($startPos = $phpcsFile->findNext($searchKeys, start: $startPos + 1)) {
            $endPos = $phpcsFile->findNext([T_STRING, T_DOC_COMMENT_TAG], start: $startPos + 1, exclude: true);
            $dependency = trim($phpcsFile->getTokensAsString($startPos, length: $endPos - $startPos));

            foreach (explode(separator: '|', string: $dependency) as $fragment) {
                $fragment = explode(separator: ' ', string: $fragment)[0] ?? '';
                $fragment = explode(separator: '(', string: $fragment)[0] ?? '';
                $fragment = explode(separator: '\\', string: $fragment)[0] ?? '';
                $fragment = preg_replace(pattern: '/[^a-z0-9_ ]/i', replacement: '', subject: $fragment);
                $usedDependencies = array_merge($usedDependencies, array_filter([$fragment]));
            }
        }

        $phpcsFile->fixer->beginChangeset();

        foreach (array_diff(array_keys($declaredDependencies), $usedDependencies) as $usage) {
            $aliasPosition = $stackPointers[$usage];
            $startPos = $phpcsFile->findPrevious([T_USE], $aliasPosition);
            $endPos = $phpcsFile->findNext([T_SEMICOLON], $aliasPosition);

            $phpcsFile->addFixableError(
                error: 'File have unused dependency %s',
                stackPtr: $aliasPosition,
                code: 'StatementUse',
                data: [$declaredDependencies[$usage]],
            );

            for ($i = $startPos; $i <= $endPos + 1; $i++) {
                $phpcsFile->fixer->replaceToken($i, content: '');
            }
        }

        $phpcsFile->fixer->endChangeset();

        return $stackPtr + 1;
    }

    public function register(): array
    {
        return [T_OPEN_TAG];
    }
}
