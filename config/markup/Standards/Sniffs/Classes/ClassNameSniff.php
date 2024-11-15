<?php

declare(strict_types=1);

namespace Standards\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ClassNameSniff implements Sniff
{
    private array $declaredNames = [];

    public function process(File $phpcsFile, mixed $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $className = $phpcsFile->getDeclarationName($stackPtr);

        if (isset($this->declaredNames[$className])) {
            $file = $this->declaredNames[$className]['file'];
            $line = $this->declaredNames[$className]['line'];
            $phpcsFile->addError(
                error: 'Duplicate "%s" was first defined in %s on line %s',
                stackPtr: $stackPtr,
                code: 'ClassName',
                data: [$className, $file, $line],
            );
        }

        $this->declaredNames[$className] = [
            'file' => $phpcsFile->getFilename(),
            'line' => $tokens[$stackPtr]['line'],
        ];

        return $phpcsFile->numTokens + 1;
    }

    public function register(): array
    {
        return [T_CLASS, T_ENUM, T_INTERFACE, T_TRAIT];
    }
}
