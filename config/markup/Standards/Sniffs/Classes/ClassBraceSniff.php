<?php

declare(strict_types=1);

namespace Standards\Sniffs\Classes;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

final class ClassBraceSniff implements Sniff
{
    #[Override]
    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        if (!isset($tokens[$stackPtr]['scope_closer'])) {
            return;
        }

        $scopeOpener = $tokens[$stackPtr]['scope_opener'];
        $scopeCloser = $tokens[$stackPtr]['scope_closer'];

        $isAnonymousClass = $tokens[$stackPtr]['code'] === T_ANON_CLASS;

        if ($isAnonymousClass) {
            $nextNonEmptyToken = $phpcsFile->findNext(
                types: Tokens::EMPTY_TOKENS,
                start: $scopeOpener + 1,
                end: $scopeCloser,
                exclude: true,
            );

            if ($nextNonEmptyToken === false) {
                $this->collapseToSingleLine($phpcsFile, $tokens, $scopeOpener, $scopeCloser);

                return;
            }
        }

        $lineStart = $phpcsFile->findFirstOnLine([T_WHITESPACE, T_INLINE_HTML], $stackPtr, exclude: true);

        while ($tokens[$lineStart]['code'] === T_CONSTANT_ENCAPSED_STRING
            && $tokens[$lineStart - 1]['code'] === T_CONSTANT_ENCAPSED_STRING
        ) {
            $lineStart = $phpcsFile->findFirstOnLine(
                types: [T_WHITESPACE, T_INLINE_HTML],
                start: $lineStart - 1,
                exclude: true,
            );
        }

        $startColumn = $tokens[$lineStart]['column'];
        $lastContent = $phpcsFile->findPrevious(
            types: [T_INLINE_HTML, T_WHITESPACE, T_OPEN_TAG],
            start: $scopeCloser - 1,
            end: $scopeOpener,
            exclude: true,
        );

        $closerLineStart = $scopeCloser;

        while ($tokens[$closerLineStart]['column'] > 1) {
            $closerLineStart--;
        }

        $isClosingLineNonEmptyInlineHtml = $tokens[$closerLineStart]['code'] === T_INLINE_HTML
            && trim($tokens[$closerLineStart]['content']) !== '';
        $hasContentBeforeClosingBrace = $tokens[$lastContent]['line'] === $tokens[$scopeCloser]['line']
            || $isClosingLineNonEmptyInlineHtml;

        if ($hasContentBeforeClosingBrace) {
            $fix = $phpcsFile->addFixableError(
                error: 'Closing brace must be on a line by itself',
                stackPtr: $scopeCloser,
                code: 'ContentBefore',
            );

            if ($fix === true) {
                if ($tokens[$lastContent]['line'] === $tokens[$scopeCloser]['line']) {
                    $phpcsFile->fixer->addNewlineBefore(stackPtr: $scopeCloser);
                } else {
                    $phpcsFile->fixer->addNewlineBefore(stackPtr: $closerLineStart + 1);
                }
            }

            return;
        }

        $closerLineStart = $phpcsFile->findFirstOnLine([T_WHITESPACE, T_INLINE_HTML], $scopeCloser, exclude: true);
        $braceIndent = $tokens[$closerLineStart]['column'];

        $isSwitchBranch = $tokens[$stackPtr]['code'] === T_DEFAULT || $tokens[$stackPtr]['code'] === T_CASE;
        $closingBraceNeedsReindent = !$isSwitchBranch && $braceIndent !== $startColumn;

        if ($closingBraceNeedsReindent) {
            $fix = $phpcsFile->addFixableError(
                error: 'Closing brace indented incorrectly; expected %s spaces, found %s',
                stackPtr: $scopeCloser,
                code: 'Indent',
                data: [$startColumn - 1, $braceIndent - 1],
            );

            if ($fix === true) {
                $diff = $startColumn - $braceIndent;

                if ($diff > 0) {
                    $phpcsFile->fixer->addContentBefore($closerLineStart, str_repeat(string: ' ', times: $diff));
                } else {
                    $phpcsFile->fixer->substrToken(stackPtr: $closerLineStart - 1, start: 0, length: $diff);
                }
            }
        }
    }

    #[Override]
    public function register(): array
    {
        return Tokens::SCOPE_OPENERS;
    }

    /**
     * @param array<int, array<string, mixed>> $tokens
     */
    private function collapseToSingleLine(File $phpcsFile, array $tokens, int $scopeOpener, int $scopeCloser): void
    {
        if ($tokens[$scopeOpener]['line'] === $tokens[$scopeCloser]['line']) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            error: 'Empty anonymous class body must be on a single line',
            stackPtr: $scopeOpener,
            code: 'EmptyBodyMultiLine',
        );

        if ($fix !== true) {
            return;
        }

        $phpcsFile->fixer->beginChangeset();

        for ($i = $scopeOpener + 1; $i < $scopeCloser; $i++) {
            $phpcsFile->fixer->replaceToken(stackPtr: $i, content: '');
        }

        $phpcsFile->fixer->endChangeset();
    }
}
