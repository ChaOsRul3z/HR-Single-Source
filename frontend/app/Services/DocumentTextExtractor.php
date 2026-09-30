<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use ZipArchive;

class DocumentTextExtractor
{
    /**
     * Extract plain text content from a given file path.
     */
    public function extract(string $filePath, ?string $mimeType = null): string
    {
        if (! file_exists($filePath)) {
            return '';
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => $this->extractPdf($filePath),
            'docx' => $this->extractDocx($filePath),
            'txt', 'md', 'json', 'csv' => file_get_contents($filePath) ?: '',
            default => $this->extractGeneric($filePath, $mimeType),
        };
    }

    /**
     * Extract text from PDF using pdftotext or stream parser.
     */
    protected function extractPdf(string $filePath): string
    {
        // Check pdftotext CLI
        if (file_exists('/usr/bin/pdftotext')) {
            $result = Process::run(['/usr/bin/pdftotext', '-layout', $filePath, '-']);
            if ($result->successful() && ! empty(trim($result->output()))) {
                return $this->cleanText($result->output());
            }
        }

        // Fallback: simple text stream extraction from PDF
        $content = file_get_contents($filePath) ?: '';
        preg_match_all('/\((.*?)\)[\r\n\s]*Tj/s', $content, $matches);
        if (! empty($matches[1])) {
            return $this->cleanText(implode(' ', $matches[1]));
        }

        preg_match_all('/\[(.*?)\][\r\n\s]*TJ/s', $content, $matches);
        if (! empty($matches[1])) {
            $extracted = '';
            foreach ($matches[1] as $item) {
                preg_match_all('/\((.*?)\)/s', $item, $inner);
                $extracted .= ' ' . implode('', $inner[1]);
            }
            return $this->cleanText($extracted);
        }

        return '';
    }

    /**
     * Extract text from DOCX (Office Open XML) without external packages.
     */
    protected function extractDocx(string $filePath): string
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (! $xml) {
            return '';
        }

        // Replace paragraph tags with newline before stripping tags
        $xml = str_replace(['</w:p>', '</w:tr>'], ["\n\n", "\n"], $xml);
        $text = strip_tags($xml);

        return $this->cleanText($text);
    }

    /**
     * Fallback extractor for generic/other text files.
     */
    protected function extractGeneric(string $filePath, ?string $mimeType = null): string
    {
        if ($mimeType && Str::startsWith($mimeType, 'text/')) {
            return file_get_contents($filePath) ?: '';
        }

        return '';
    }

    /**
     * Normalize extracted text.
     */
    protected function cleanText(string $text): string
    {
        // Normalize line breaks
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        // Replace 3+ consecutive newlines with 2
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text ?? '');
    }

    /**
     * Generate a concise summary/snippet from extracted text.
     */
    public function generateSnippet(string $text, int $maxLength = 220): string
    {
        $paragraphs = array_filter(
            array_map('trim', explode("\n", $text)),
            fn ($p) => strlen($p) > 25
        );

        if (! empty($paragraphs)) {
            $first = reset($paragraphs);
            return Str::limit($first, $maxLength);
        }

        return Str::limit(trim($text), $maxLength);
    }
}
