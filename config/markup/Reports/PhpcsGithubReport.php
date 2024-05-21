<?php

declare(strict_types=1);

namespace Reports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Reports\Report;

class PhpcsGithubReport implements Report
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

        foreach ($messages as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    echo $this->format(
                        message: $this->convert($error['message'], $phpcsFile->config->encoding),
                        type: $error['type'] === 'ERROR' ? 'error' : 'warning',
                        parameters: [
                            'file' => $report['filename'],
                            'line' => $line,
                            'col' => $column,
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
        echo $cachedData;
    }

    private function format(string $message, string $type, array $parameters): string
    {
        $message = $this->escape($message);

        foreach ($parameters as $key => $value) {
            $parameters[$key] = sprintf('%s=%s', $key, $value);
        }

        return sprintf('::%s %s::%s', $type, implode(separator: ' ', array: $parameters), $message) . PHP_EOL;
    }

    private function convert(string $message, string $encoding): string
    {
        return $encoding !== 'utf-8' ? mb_convert_encoding($message, 'utf-8', $encoding) : $message;
    }

    private function escape(string $string): string
    {
        $replacements = ['~\n~' => '%0A'];

        return preg_replace(array_keys($replacements), array_values($replacements), $string);
    }
}
