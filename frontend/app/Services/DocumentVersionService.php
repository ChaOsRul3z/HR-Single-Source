<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentVersionService
{
    /**
     * Ingest and store a new document or revision with automatic versioning and archiving.
     *
     * @param array{
     *     title: string,
     *     document_code?: string|null,
     *     department?: string|null,
     *     file_id?: int|null,
     *     user_id?: int|null,
     *     extracted_text?: string|null,
     *     summary?: string|null,
     *     is_restricted?: bool,
     *     effective_date?: string|null,
     *     version?: int|null
     * } $data
     */
    public function ingest(array $data): Document
    {
        return DB::transaction(function () use ($data) {
            $code = $data['document_code'] ?? Str::slug($data['title']);
            if (empty($code)) {
                $code = 'DOC-' . Str::upper(Str::random(6));
            }

            // Determine version number
            if (! isset($data['version']) || empty($data['version'])) {
                $latestVersion = Document::where('document_code', $code)->max('version');
                $version = $latestVersion ? ($latestVersion + 1) : 1;
            } else {
                $version = (int) $data['version'];
            }

            // AUTO-ARCHIVING GUARDRAIL:
            // When a new version is uploaded, mark any prior active versions of this document code as 'archived'
            Document::where('document_code', $code)
                ->where('status', 'active')
                ->update(['status' => 'archived']);

            // Create new active single-source-of-truth document
            return Document::create([
                'title' => $data['title'],
                'document_code' => $code,
                'department' => $data['department'] ?? 'HR',
                'file_id' => $data['file_id'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'version' => $version,
                'status' => 'active',
                'is_restricted' => $data['is_restricted'] ?? false,
                'effective_date' => $data['effective_date'] ?? now()->toDateString(),
                'summary' => $data['summary'] ?? null,
                'extracted_text' => $data['extracted_text'] ?? null,
            ]);
        });
    }

    /**
     * Archive an existing document version.
     */
    public function archive(Document $document): bool
    {
        return $document->archive();
    }

    /**
     * Set a specific version back to the active single source of truth.
     */
    public function activate(Document $document): bool
    {
        return DB::transaction(function () use ($document) {
            // Archive current active
            Document::where('document_code', $document->document_code)
                ->where('id', '!=', $document->id)
                ->where('status', 'active')
                ->update(['status' => 'archived']);

            return $document->activate();
        });
    }

    /**
     * Retrieve all versions of a document series.
     *
     * @return Collection<int, Document>
     */
    public function getVersionHistory(string $documentCode): Collection
    {
        return Document::where('document_code', $documentCode)
            ->with(['file', 'user'])
            ->orderBy('version', 'desc')
            ->get();
    }
}
