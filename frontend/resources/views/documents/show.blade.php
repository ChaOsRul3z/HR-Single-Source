<x-layouts::app :title="$document->title . ' (v' . $document->version . ')'">
    <div class="flex w-full flex-1 flex-col gap-8 font-sans antialiased text-zinc-900 dark:text-zinc-100 max-w-5xl mx-auto">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm text-zinc-500">
            <a href="{{ route('dashboard') }}" class="hover:text-zinc-800 dark:hover:text-zinc-200 transition">Dashboard</a>
            <span>›</span>
            <a href="{{ route('search') }}" class="hover:text-zinc-800 dark:hover:text-zinc-200 transition">Zoeken</a>
            <span>›</span>
            <span class="text-zinc-900 dark:text-white font-medium">{{ $document->title }}</span>
        </nav>

        <!-- Document Header -->
        <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 md:p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white md:text-3xl">{{ $document->title }}</h1>
                        <span class="rounded bg-blue-50 dark:bg-blue-950/60 px-2.5 py-0.5 font-mono text-sm font-semibold text-blue-700 dark:text-blue-300">v{{ $document->version }}</span>
                        @if ($document->isActive())
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Actuele Single Source of Truth
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-zinc-400"></span> Gearchiveerde Versie
                            </span>
                        @endif
                        @if ($document->is_restricted)
                            <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 dark:bg-amber-950/50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:text-amber-300">
                                🔒 HR Vertrouwelijk
                            </span>
                        @endif
                    </div>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 font-mono">
                        Code: {{ $document->document_code }}
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @if ($document->file)
                        <a href="{{ route('documents.download', $document) }}" class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 transition">
                            <svg class="h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download Origineel
                        </a>
                    @endif

                    @if (auth()->user()?->isHr())
                        <form method="POST" action="{{ route('documents.toggle-status', $document) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition
                                {{ $document->isActive()
                                    ? 'border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300'
                                    : 'border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300' }}">
                                {{ $document->isActive() ? '📦 Archiveer deze versie' : '✅ Heractiveer als Single Source' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Metadata Grid -->
            <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4 border-t border-zinc-100 dark:border-zinc-800 pt-6">
                @foreach ([
                    ['Afdeling', $document->department],
                    ['Geldig vanaf', $document->effective_date ? \Illuminate\Support\Carbon::parse($document->effective_date)->format('d-m-Y') : '—'],
                    ['Geüpload door', $document->user?->name ?? 'Onbekend'],
                    ['Laatst bijgewerkt', $document->updated_at->format('d-m-Y H:i')],
                ] as [$label, $value])
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ $label }}</dt>
                        <dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $value }}</dd>
                    </div>
                @endforeach
            </div>

            @if ($document->file)
                <div class="mt-4 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Bronbestand</dt>
                    <dd class="mt-1 flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                        <svg class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        {{ $document->file->original_name }}
                        <span class="text-xs text-zinc-400">({{ $document->file->readable_size }})</span>
                    </dd>
                </div>
            @endif
        </div>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <!-- Summary -->
        @if ($document->summary)
            <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-6 dark:border-blue-900/30 dark:bg-blue-950/20">
                <h2 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Samenvatting
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $document->summary }}</p>
            </div>
        @endif

        <!-- Extracted Full Text -->
        @if ($document->extracted_text)
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-zinc-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Volledige Geëxtraheerde Inhoud
                </h2>
                <div class="mt-4 whitespace-pre-wrap text-sm leading-relaxed text-zinc-700 dark:text-zinc-300 font-mono bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-zinc-100 dark:border-zinc-800 max-h-[600px] overflow-y-auto">{{ $document->extracted_text }}</div>
            </div>
        @endif

        <!-- Version History -->
        @if ($history->count() > 1)
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Versiegeschiedenis ({{ $history->count() }} versies)
                </h2>

                <div class="space-y-3">
                    @foreach ($history as $version)
                        <a href="{{ route('documents.show', $version) }}"
                           class="flex items-center justify-between rounded-xl border p-4 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/40
                               {{ $version->id === $document->id
                                   ? 'border-blue-300 bg-blue-50/30 dark:border-blue-800/50 dark:bg-blue-950/20'
                                   : 'border-zinc-200 dark:border-zinc-800' }}">
                            <div class="flex items-center gap-3">
                                <span class="rounded bg-blue-50 dark:bg-blue-950/60 px-2.5 py-0.5 font-mono text-sm font-bold text-blue-700 dark:text-blue-300">v{{ $version->version }}</span>
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $version->title }}
                                        @if ($version->id === $document->id)
                                            <span class="text-xs text-blue-600 dark:text-blue-400">(huidige weergave)</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-zinc-500">
                                        Geldig: {{ $version->effective_date ? \Illuminate\Support\Carbon::parse($version->effective_date)->format('d-m-Y') : '—' }}
                                        · Geüpload: {{ $version->created_at->format('d-m-Y H:i') }}
                                        @if ($version->user)
                                            · Door: {{ $version->user->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div>
                                @if ($version->isActive())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Actief
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                        Gearchiveerd
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-layouts::app>
