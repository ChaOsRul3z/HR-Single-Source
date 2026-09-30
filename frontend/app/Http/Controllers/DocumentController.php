<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    private function mockDocuments(): array
    {
        return [
            ['id' => 1, 'title' => 'Verlofbeleid', 'department' => 'HR', 'version' => 2, 'status' => 'active', 'uploaded_at' => '2026-09-01', 'snippet' => 'Vaderschapsverlof bedraagt 20 dagen.'],
            ['id' => 2, 'title' => 'Verlofbeleid', 'department' => 'HR', 'version' => 1, 'status' => 'archived', 'uploaded_at' => '2025-03-10', 'snippet' => 'Vaderschapsverlof bedraagt 10 dagen.'],
            ['id' => 3, 'title' => 'Gedragscode', 'department' => 'Legal', 'version' => 1, 'status' => 'active', 'uploaded_at' => '2026-01-15', 'snippet' => 'Medewerkers melden belangenconflicten aan HR.'],
        ];
    }

    public function search(Request $request)
    {
        $q = strtolower($request->query('q', ''));
        $includeArchived = $request->boolean('archived');

        $results = collect($this->mockDocuments())
            ->when(! $includeArchived, fn ($c) => $c->where('status', 'active'))
            ->when($q !== '', fn ($c) => $c->filter(
                fn ($d) => str_contains(strtolower($d['title'].' '.$d['snippet']), $q)
            ));

        return view('search', [
            'results' => $results,
            'q' => $request->query('q', ''),
            'includeArchived' => $includeArchived,
        ]);
    }
}