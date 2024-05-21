<?php

declare(strict_types=1);

namespace Reports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Reports\Report;

class PhpcsTeamcityReport implements Report
{
    private array $inspectionTypes = [];

    public function generateFileReport(
        mixed $report,
        File $phpcsFile,
        mixed $showSources = false,
        mixed $width = 80,
    ): bool {
        $errorCount = $phpcsFile->getErrorCount();
        $warningCount = $phpcsFile->getWarningCount();
        $messages = ($errorCount !== 0 || $warningCount !== 0) ? $report['messages'] : [];

        foreach ($messages as $line => $lineErrors) {
            foreach ($lineErrors as $colErrors) {
                foreach ($colErrors as $error) {
                    if (!array_key_exists($error['source'], $this->inspectionTypes)) {
                        $this->inspectionTypes[$error['source']] = $this->format(
                            message: 'inspectionType',
                            parameters: [
                                'id' => $error['source'],
                                'name' => $error['source'],
                                'category' => $this->extractCategoryFromSource($error['source']),
                                'description' => 'CodeSniffer inspection',
                            ],
                        );
                    }

                    echo $this->format(
                        message: 'inspection',
                        parameters: [
                            'typeId' => $error['source'],
                            'file' => $report['filename'],
                            'line' => $line,
                            'message' => $this->convert($error['message'], $phpcsFile->config->encoding),
                            'SEVERITY' => $error['type'],
                            'fixable' => $error['fixable'],
                        ],
                    );
                }
            }
        }

        return !empty($messages);
    }

    public function generate(
        mixed $cachedData,
        mixed $totalFiles,
        mixed $totalErrors,
        mixed $totalWarnings,
        mixed $totalFixable,
        mixed $showSources = false,
        mixed $width = 80,
        mixed $interactive = false,
        mixed $toScreen = true,
    ): void {
        foreach ($this->inspectionTypes as $inspectionType) {
            echo $inspectionType;
        }

        echo $cachedData;
    }

    private function extractCategoryFromSource(string $source): string
    {
        $category = 'CodeSniffer';
        $pattern = '~^([^\.]+\.[^\.]+)\.[^\.]+\.[^\.]+$~';

        preg_match($pattern, $source, $matches);

        return $category . (isset($matches[1]) ? ' ' . $matches[1] : '');
    }

    private function format(string $message, array $parameters): string
    {
        foreach ($parameters as $key => $value) {
            $parameters[$key] = sprintf('%s=\'%s\'', $key, (is_string($value) ? $this->escape($value) : $value));
        }

        return sprintf('##teamcity[%s %s]', $message, implode(separator: ' ', array: $parameters)) . PHP_EOL;
    }

    private function convert(string $message, string $encoding): string
    {
        return $encoding !== 'utf-8' ? mb_convert_encoding($message, 'utf-8', $encoding) : $message;
    }

    private function escape(string $string): string
    {
        $replacements = ['~\n~' => '|n', '~\r~' => '|r', '~([\'\|\[\]])~' => '|$1'];

        return preg_replace(array_keys($replacements), array_values($replacements), $string);
    }
}
