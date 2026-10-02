<?php

declare(strict_types=1);

namespace Reports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Reports\Report;

final class PhpcsTeamcityReport implements Report
{
    public function generateFileReport(
        mixed $report,
        File $phpcsFile,
        mixed $showSources = false,
        mixed $width = 80,
    ): bool {
        $errorCount = $phpcsFile->getErrorCount();
        $warningCount = $phpcsFile->getWarningCount();
        $messages = ($errorCount !== 0 || $warningCount !== 0) ? $report['messages'] : [];
        $inspectionTypes = [];

        foreach ($messages as $line => $lineErrors) {
            foreach ($lineErrors as $colErrors) {
                foreach ($colErrors as $error) {
                    if (!isset($inspectionTypes[$error['source']])) {
                        $inspectionTypes[$error['source']] = true;

                        echo $this->format(
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
        // Worker state is not shared, so collect types from the cached report output.
        $inspectionTypes = [];
        $inspections = '';

        foreach (explode(PHP_EOL, $cachedData) as $line) {
            if (str_starts_with($line, '##teamcity[inspectionType ')) {
                $inspectionTypes[$line] = $line . PHP_EOL;
            } elseif ($line !== '') {
                $inspections .= $line . PHP_EOL;
            }
        }

        foreach ($inspectionTypes as $inspectionType) {
            echo $inspectionType;
        }

        echo $inspections;
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
        return strtr($string, [
            '|' => '||',
            "'" => "|'",
            "\n" => '|n',
            "\r" => '|r',
            '[' => '|[',
            ']' => '|]',
        ]);
    }
}
