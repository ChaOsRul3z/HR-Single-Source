<?php

use App\Models\Document;
use App\Models\User;
use App\Services\DocumentSearchService;
use App\Services\DocumentVersionService;

beforeEach(function () {
    $this->seed();
});

test('version service auto-archives prior versions when a new version is ingested', function () {
    $versionService = app(DocumentVersionService::class);

    // Ingest version 1
    $v1 = $versionService->ingest([
        'title' => 'Telewerkbeleid',
        'document_code' => 'telewerkbeleid',
        'department' => 'HR',
        'summary' => 'Oud telewerkbeleid: 1 dag per week thuis.',
        'extracted_text' => 'Oud telewerkbeleid: 1 dag per week thuis.',
    ]);

    expect($v1->version)->toBe(1)
        ->and($v1->status)->toBe('active');

    // Ingest version 2 of the same document_code
    $v2 = $versionService->ingest([
        'title' => 'Telewerkbeleid',
        'document_code' => 'telewerkbeleid',
        'department' => 'HR',
        'summary' => 'Nieuw telewerkbeleid: 3 dagen per week thuis.',
        'extracted_text' => 'Nieuw telewerkbeleid: 3 dagen per week thuis.',
    ]);

    // Check version 2 is active, version 1 was auto-archived
    expect($v2->version)->toBe(2)
        ->and($v2->status)->toBe('active');

    $v1->refresh();
    expect($v1->status)->toBe('archived');
});

test('search engine returns matching snippet and citations from active single source of truth', function () {
    $searchService = app(DocumentSearchService::class);

    // Run query for paternity leave
    $results = $searchService->search(query: 'vaderschapsverlof', includeArchived: false);

    expect($results)->not->toBeEmpty();
    $first = $results->first();

    // Verify it returned the active version (v2), not the old archived v1
    expect($first['document']->title)->toBe('Verlofbeleid')
        ->and($first['document']->version)->toBe(2)
        ->and($first['document']->status)->toBe('active')
        ->and($first['citation'])->toContain('Verlofbeleid (v2, HR)')
        ->and($first['matched_paragraph'])->toContain('20 werkdagen');
});

test('search engine includes archived version only when requested', function () {
    $searchService = app(DocumentSearchService::class);

    // Without archived flag
    $activeResults = $searchService->search(query: 'vaderschapsverlof', includeArchived: false);
    $activeVersions = $activeResults->pluck('document.version')->all();
    expect($activeVersions)->toContain(2)
        ->and($activeVersions)->not->toContain(1);

    // With archived flag
    $allResults = $searchService->search(query: 'vaderschapsverlof', includeArchived: true);
    $allVersions = $allResults->pluck('document.version')->all();
    expect($allVersions)->toContain(1)
        ->and($allVersions)->toContain(2);
});

test('role-based guardrail prevents regular employees from accessing restricted files (Aikido security audit)', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $admin = User::factory()->create(['role' => 'admin']);

    $searchService = app(DocumentSearchService::class);

    // Employee search for executive bonuses
    $employeeResults = $searchService->search(query: 'Directiebonussen', user: $employee);
    expect($employeeResults)->toBeEmpty();

    // Admin search for executive bonuses
    $adminResults = $searchService->search(query: 'Directiebonussen', user: $admin);
    expect($adminResults)->not->toBeEmpty();
    expect($adminResults->first()['document']->title)->toContain('Vertrouwelijk Beloningskader');

    // Admin portal endpoint access check
    $this->actingAs($employee)->get(route('admin.documents'))
        ->assertForbidden();

    $this->actingAs($admin)->get(route('admin.documents'))
        ->assertOk();
});
