<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DocumentSearchService
{
    /**
     * Search the single source repository with plain text / natural language queries.
     *
     * @return Collection<int, array{
     *     document: Document,
     *     score: float,
     *     citation: string,
     *     snippet: string,
     *     matched_paragraph: string
     * }>
     */
    public function search(
        string $query,
        ?User $user = null,
        bool $includeArchived = false,
        ?string $department = null
    ): Collection {
        $query = trim($query);

        $builder = Document::query()
            ->with(['file', 'user'])
            ->accessibleBy($user);

        if (! $includeArchived) {
            $builder->active();
        }

        if ($department) {
            $builder->where('department', $department);
        }

        if ($query === '') {
            return $builder->orderBy('updated_at', 'desc')
                ->get()
                ->map(fn (Document $doc) => [
                    'document' => $doc,
                    'score' => 1.0,
                    'citation' => "{$doc->title} (v{$doc->version}, {$doc->department})",
                    'snippet' => $doc->summary ?? Str::limit($doc->extracted_text ?? '', 160),
                    'matched_paragraph' => $doc->summary ?? '',
                ]);
        }

        $tokens = $this->tokenize($query);

        // Pre-filter documents in database containing any token in title, summary or content
        $documents = $builder->where(function ($q) use ($tokens, $query) {
            $q->where('title', 'like', "%{$query}%")
                ->orWhere('summary', 'like', "%{$query}%")
                ->orWhere('extracted_text', 'like', "%{$query}%");

            foreach ($tokens as $token) {
                $q->orWhere('title', 'like', "%{$token}%")
                  ->orWhere('extracted_text', 'like', "%{$token}%");
            }
        })->get();

        $results = collect();

        foreach ($documents as $doc) {
            $evaluation = $this->scoreAndExtractSnippet($doc, $query, $tokens);
            if ($evaluation['score'] > 0) {
                $results->push([
                    'document' => $doc,
                    'score' => $evaluation['score'],
                    'citation' => "{$doc->title} (v{$doc->version}, {$doc->department})",
                    'snippet' => $evaluation['snippet'],
                    'matched_paragraph' => $evaluation['paragraph'],
                ]);
            }
        }

        return $results->sortByDesc('score')->values();
    }

    /**
     * Score a document against query tokens and extract the most relevant paragraph.
     *
     * @param array<int, string> $tokens
     * @return array{score: float, snippet: string, paragraph: string}
     */
    protected function scoreAndExtractSnippet(Document $doc, string $rawQuery, array $tokens): array
    {
        $textToSearch = ($doc->title . "\n\n" . ($doc->summary ?? '') . "\n\n" . ($doc->extracted_text ?? ''));
        $paragraphs = array_filter(
            array_map('trim', preg_split('/\n{1,}/', $textToSearch)),
            fn ($p) => strlen($p) > 10
        );

        $bestParagraph = $doc->summary ?? Str::limit($doc->extracted_text ?? '', 200);
        $bestScore = 0.0;

        $lowerRaw = mb_strtolower($rawQuery);

        // Bonus if query matches title exactly
        if (str_contains(mb_strtolower($doc->title), $lowerRaw)) {
            $bestScore += 25.0;
        }

        foreach ($paragraphs as $paragraph) {
            $pLower = mb_strtolower($paragraph);
            $pScore = 0.0;

            // Exact phrase match gives huge boost
            if (str_contains($pLower, $lowerRaw)) {
                $pScore += 15.0;
            }

            // Token overlap
            $matches = 0;
            foreach ($tokens as $token) {
                if (str_contains($pLower, $token)) {
                    $matches++;
                    $pScore += 3.0;
                }
            }

            if ($matches === count($tokens) && count($tokens) > 1) {
                $pScore += 5.0; // All tokens in paragraph bonus
            }

            if ($pScore > $bestScore) {
                $bestScore = $pScore;
                $bestParagraph = $paragraph;
            }
        }

        // Active document status boost (encourages latest single-source-of-truth)
        if ($doc->isActive()) {
            $bestScore += 2.0;
        }

        // Form clean snippet
        $snippet = Str::limit($bestParagraph, 220);

        return [
            'score' => round($bestScore, 2),
            'snippet' => $snippet,
            'paragraph' => $bestParagraph,
        ];
    }

    /**
     * Tokenize natural language query into keywords.
     *
     * @return array<int, string>
     */
    protected function tokenize(string $query): array
    {
        $stopwords = ['what', 'is', 'our', 'the', 'a', 'an', 'in', 'on', 'for', 'of', 'and', 'to', 'wat', 'is', 'ons', 'onze', 'het', 'de', 'een', 'in', 'voor', 'van', 'en'];

        $words = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($query));
        $tokens = [];

        foreach ($words as $w) {
            if (mb_strlen($w) >= 2 && ! in_array($w, $stopwords, true)) {
                $tokens[] = $w;
            }
        }

        return array_unique($tokens);
    }
}
