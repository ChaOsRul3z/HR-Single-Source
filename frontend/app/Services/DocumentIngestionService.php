<?php

namespace App\Services;

use App\Models\Document;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class DocumentIngestionService
{
    public function __construct(
        protected DocumentTextExtractor $extractor,
        protected DocumentVersionService $versionService
    ) {}

    /**
     * Ingest an uploaded file, extract its contents, and publish as single-source-of-truth document.
     *
     * @param array{
     *     title?: string|null,
     *     department?: string|null,
     *     is_restricted?: bool|null,
     *     effective_date?: string|null
     * } $metadata
     */
    public function ingestUploadedFile(UploadedFile $uploadedFile, ?User $user = null, array $metadata = []): Document
    {
        $hash = hash_file('sha256', $uploadedFile->getRealPath());
        $originalName = $uploadedFile->getClientOriginalName();
        $filename = uniqid() . '_' . $originalName;

        $path = $uploadedFile->storeAs('documents', $filename, 'public');

        $fileModel = File::create([
            'original_name' => $originalName,
            'storage_path'  => $path,
            'size'          => $uploadedFile->getSize(),
            'mime_type'     => $uploadedFile->getMimeType(),
            'file_hash'     => $hash,
        ]);

        // Extract text
        $fullPath = storage_path('app/public/' . $path);
        $extractedText = $this->extractor->extract($fullPath, $uploadedFile->getMimeType());

        // Derive title if not given
        $title = ! empty($metadata['title'])
            ? $metadata['title']
            : Str::headline(pathinfo($originalName, PATHINFO_FILENAME));

        // Generate summary snippet
        $summary = $this->extractor->generateSnippet($extractedText);

        return $this->versionService->ingest([
            'title' => $title,
            'document_code' => Str::slug($title),
            'department' => $metadata['department'] ?? 'HR',
            'file_id' => $fileModel->id,
            'user_id' => $user?->id,
            'extracted_text' => $extractedText,
            'summary' => $summary,
            'is_restricted' => (bool) ($metadata['is_restricted'] ?? false),
            'effective_date' => $metadata['effective_date'] ?? now()->toDateString(),
        ]);
    }
}
