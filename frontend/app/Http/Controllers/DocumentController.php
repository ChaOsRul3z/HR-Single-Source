<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentSearchService;
use App\Services\DocumentVersionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentSearchService $searchService,
        protected DocumentVersionService $versionService
    ) {}

    /**
     * HR Admin Portal: View and manage all company documents and version lifecycles.
     */
    public function admin(Request $request)
    {
<<<<<<< HEAD
        $user = $request->user();

        // Aikido Security Guardrail: Only HR/Admin can access document administration
        if (! $user || ! $user->isHr()) {
            abort(403, 'Unauthorized. Access to HR document administration is restricted.');
        }

        $department = $request->query('department');
        $status = $request->query('status');

        $query = Document::query()
            ->with(['file', 'user'])
            ->orderBy('title')
            ->orderBy('version', 'desc');

        if ($department) {
            $query->where('department', $department);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $documents = $query->get();

        $departments = Document::query()
            ->distinct()
            ->pluck('department')
            ->sort()
            ->values();

        return view('admin.documents', [
            'documents' => $documents,
            'departments' => $departments,
            'selectedDepartment' => $department,
            'selectedStatus' => $status,
        ]);
    }

    /**
     * Single Source Search Hub: Natural language query engine.
     */
=======
        return [
            ['id' => 1, 'title' => 'Verlofbeleid', 'department' => 'HR', 'version' => 2, 'status' => 'active', 'uploaded_at' => '2026-09-01', 'snippet' => 'Vaderschapsverlof bedraagt 20 dagen.'],
            ['id' => 2, 'title' => 'Verlofbeleid', 'department' => 'HR', 'version' => 1, 'status' => 'archived', 'uploaded_at' => '2025-03-10', 'snippet' => 'Vaderschapsverlof bedraagt 10 dagen.'],
            ['id' => 3, 'title' => 'Gedragscode', 'department' => 'Legal', 'version' => 1, 'status' => 'active', 'uploaded_at' => '2026-01-15', 'snippet' => 'Medewerkers melden belangenconflicten aan HR.'],
        ];
    }

>>>>>>> 428235c8af0f3fb8c52d30a59a3af5eb4d3f1a12
    public function search(Request $request)
    {
        $user = $request->user();
        $q = (string) $request->query('q', '');
        $includeArchived = $request->boolean('archived');
        $department = $request->query('department');

        $results = $this->searchService->search(
            query: $q,
            user: $user,
            includeArchived: $includeArchived,
            department: $department ?: null
        );

        $departments = Document::accessibleBy($user)
            ->distinct()
            ->pluck('department')
            ->sort()
            ->values();

        return view('search', [
            'results' => $results,
            'q' => $q,
            'includeArchived' => $includeArchived,
            'department' => $department,
            'departments' => $departments,
        ]);
    }

    /**
     * Display a specific document's details.
     */
    public function show(Request $request, Document $document)
    {
        Gate::authorize('view', $document);

        $document->load(['file', 'user']);
        $history = $this->versionService->getVersionHistory($document->document_code);

        return view('documents.show', [
            'document' => $document,
            'history' => $history,
        ]);
    }

    /**
     * Securely download an ingested document file.
     */
    public function download(Request $request, Document $document): StreamedResponse
    {
        // Aikido Guardrail: check IDOR / restricted access
        Gate::authorize('download', $document);

        if (! $document->file || ! Storage::disk('public')->exists($document->file->storage_path)) {
            abort(404, 'The requested document file could not be found.');
        }

        return Storage::disk('public')->download(
            $document->file->storage_path,
            $document->file->original_name
        );
    }

    /**
     * Toggle document version status (activate or archive).
     */
    public function toggleStatus(Request $request, Document $document)
    {
        Gate::authorize('manageStatus', $document);

        if ($document->isActive()) {
            $this->versionService->archive($document);
            $message = "Document {$document->title} (v{$document->version}) is nu gearchiveerd.";
        } else {
            $this->versionService->activate($document);
            $message = "Document {$document->title} (v{$document->version}) is ingesteld als de enige actieve bron van waarheid.";
        }

        return back()->with('status', $message);
    }
}