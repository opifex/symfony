<?php

declare(strict_types=1);

namespace Standards\Sniffs\Statements;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class StatementUseSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $allDependencies = $this->getAllDependencies($phpcsFile, $stackPtr);
        $dependenciesEndPtr = $allDependencies[array_key_last($allDependencies)]['end'] ?? $stackPtr;
        $usedTokens = array_flip($this->getUsedTokens($phpcsFile, $dependenciesEndPtr));

        foreach ($allDependencies as $key => $dependency) {
            if (isset($usedTokens[$dependency['alias']])) {
                unset($allDependencies[$key]);
            }
        }

        $phpcsFile->fixer->beginChangeset();

        foreach ($allDependencies as $dependency) {
            $phpcsFile->addFixableError(
                error: 'File have unused dependency %s',
                stackPtr: $dependency['start'],
                code: 'StatementUse',
                data: [$dependency['name']],
            );

            for ($index = $dependency['start']; $index <= $dependency['end'] + 1; $index++) {
                $phpcsFile->fixer->replaceToken($index, content: '');
            }
        }

        $phpcsFile->fixer->endChangeset();
    }

    #[Override]
    public function register(): array
    {
        return [T_OPEN_TAG];
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    private function getAllDependencies(File $phpcsFile, mixed $stackPtr): array
    {
        $lastPtr = $phpcsFile->findNext([T_CLASS, T_ENUM, T_INTERFACE, T_TRAIT], start: $stackPtr + 1) ?? null;
        $allDependencies = [];

        while ($stackPtr = $phpcsFile->findNext([T_USE], start: $stackPtr + 1, end: $lastPtr)) {
            $startPtr = $phpcsFile->findNext([T_WHITESPACE], start: $stackPtr + 1, exclude: true);
            $endPtr = $phpcsFile->findNext([T_SEMICOLON], start: $startPtr + 1);
            $dependencyName = $phpcsFile->getTokensAsString($startPtr, length: $endPtr - $startPtr);
            $dependencyAlias = $this->getDependencyAlias($dependencyName);

            $allDependencies[] = [
                'name' => $dependencyName,
                'alias' => $dependencyAlias,
                'start' => $stackPtr,
                'end' => $endPtr,
            ];
        }

        return $allDependencies;
    }

    private function getDependencyAlias(string $dependency): string
    {
        $dependencyAlias = strrchr($dependency, needle: ' as ') ?: $dependency;
        $dependencyAlias = strrchr($dependencyAlias, needle: '\\') ?: $dependencyAlias;

        return trim($dependencyAlias, characters: '\\ ');
    }

    /**
     * @return string[]
     */
    private function getUsedTokens(File $phpcsFile, mixed $stackPtr): array
    {
        $tokens = $phpcsFile->getTokens();
        $tokensSearchKeys = [T_STRING, T_DOC_COMMENT_STRING, T_DOC_COMMENT_WHITESPACE];
        $tokensPreviousTypes = ['T_DOUBLE_COLON', 'T_FUNCTION', 'T_OBJECT_OPERATOR'];
        $tokensUsed = [];

        while ($stackPtr = $phpcsFile->findNext($tokensSearchKeys, start: $stackPtr + 1)) {
            $previousToken = $phpcsFile->findPrevious([T_WHITESPACE], start: $stackPtr - 1, exclude: true);

            if (!in_array($tokens[$previousToken]['type'], $tokensPreviousTypes)) {
                $tokenContent = strtok($tokens[$stackPtr]['content'], token: '$');
                $tokenName = trim(str_replace(search: ['(', ')', '[', ']', ' '], replace: '', subject: $tokenContent));
                $tokensUsed = array_merge($tokensUsed, preg_split(pattern: '/[|&,<>]/', subject: $tokenName));
            }
        }

        return array_unique(array_filter($tokensUsed));
    }
}
