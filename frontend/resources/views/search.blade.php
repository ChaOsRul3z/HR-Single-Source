<x-layouts::app :title="__('AI Single Source Zoeker')">
    <div class="flex w-full flex-1 flex-col gap-8 font-sans antialiased text-zinc-900 dark:text-zinc-100 max-w-5xl mx-auto">

        <!-- Search Header & Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-[#00216B] p-8 text-white shadow-xl md:p-10">
            <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-blue-400/10 blur-3xl"></div>
            <div class="absolute -left-20 -bottom-20 h-72 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>

            <div class="relative z-10 max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1 text-xs font-semibold uppercase tracking-wider text-blue-200 backdrop-blur-md">
                    <span class="h-2 w-2 rounded-full bg-[#00D364] animate-pulse"></span> Slimme Beleid- & Kennishub
                </div>
                <h1 class="mt-4 text-2xl font-extrabold tracking-tight md:text-4xl">Stel uw vraag aan de Single Source of Truth.</h1>
                <p class="mt-2 text-sm text-blue-100/90 md:text-base leading-relaxed">
                    Doorzoek direct actuele HR-policies, arbeidsvoorwaarden en reglementen. U ontvangt uitsluitend de geldige, actuele versie met bronverwijzing.
                </p>

                <!-- Search Input Form -->
                <form method="GET" action="{{ route('search') }}" class="mt-6">
                    <div class="flex items-center gap-2 rounded-2xl bg-white p-2 shadow-2xl dark:bg-zinc-900 border border-white/20">
                        <div class="pl-3 text-zinc-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text"
                               name="q"
                               value="{{ $q }}"
                               placeholder="Typ een vraag of trefwoord (bv. vaderschapsverlof, telewerk, belangenconflict)..."
                               autofocus
                               class="flex-1 border-0 bg-transparent px-2 py-2 text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-0 text-sm md:text-base">
                        <button type="submit" class="rounded-xl bg-[#008963] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#00B563] shadow-md">
                            Zoek
                        </button>
                    </div>

                    <!-- Search Filters & Guardrails -->
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-4 text-xs text-blue-100/80">
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox"
                                       name="archived"
                                       value="1"
                                       {{ $includeArchived ? 'checked' : '' }}
                                       onchange="this.form.submit()"
                                       class="h-3.5 w-3.5 rounded border-white/40 bg-white/20 text-[#008963] focus:ring-0">
                                <span>Toon ook historische / gearchiveerde versies</span>
                            </label>

                            @if ($departments->isNotEmpty())
                                <div class="flex items-center gap-1.5">
                                    <span>Afdeling:</span>
                                    <select name="department" onchange="this.form.submit()" class="rounded-md border-0 bg-white/20 px-2 py-1 text-xs text-white focus:outline-none focus:ring-1 focus:ring-white/50">
                                        <option value="" class="text-zinc-900">Alle</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept }}" {{ $department === $dept ? 'selected' : '' }} class="text-zinc-900">{{ $dept }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        <div class="inline-flex items-center gap-1 text-[11px] text-blue-200">
                            <svg class="h-3.5 w-3.5 text-[#00D364]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>{{ $includeArchived ? 'Inclusief archief' : 'Single Source actief filter aan' }}</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Suggestion Pills -->
        @if (! $q)
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="text-zinc-500 font-medium">Veelgestelde vragen:</span>
                @foreach (['Hoeveel dagen vaderschapsverlof?', 'Wat is het thuiswerkbeleid?', 'Regels rond belangenconflicten', 'Wettelijke vakantiedagen'] as $pill)
                    <a href="{{ route('search', ['q' => $pill]) }}" class="rounded-full border border-zinc-200 bg-white px-3 py-1 text-zinc-700 hover:border-blue-500 hover:text-blue-600 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 transition shadow-sm">
                        {{ $pill }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Results Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    @if ($q)
                        Zoekresultaten voor <span class="text-blue-600 dark:text-blue-400">"{{ $q }}"</span>
                        <span class="ml-2 text-xs font-normal text-zinc-400">({{ $results->count() }} gevonden)</span>
                    @else
                        Alle beschikbare documenten van waarheid
                    @endif
                </h2>
            </div>

            @forelse ($results as $item)
                @php
                    $doc = $item['document'];
                @endphp
                <div class="group relative rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm transition hover:shadow-md hover:border-blue-500/40 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                    {{ $doc->title }}
                                </h3>
                                <span class="rounded bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 font-mono text-xs font-semibold text-blue-700 dark:text-blue-300">
                                    v{{ $doc->version }}
                                </span>
                                <span class="rounded-md bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:text-zinc-300">
                                    {{ $doc->department }}
                                </span>
                                @if ($doc->isActive())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Actuele Bron
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-zinc-400"></span> Gearchiveerd
                                    </span>
                                @endif

                                @if ($doc->is_restricted)
                                    <span class="rounded-md bg-amber-50 dark:bg-amber-950/50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:text-amber-300">
                                        HR Vertrouwelijk
                                    </span>
                                @endif
                            </div>

                            <p class="mt-1 text-xs text-zinc-400 font-mono">
                                Bronverwijzing: {{ $item['citation'] }} • Geldig sinds {{ $doc->effective_date ? \Illuminate\Support\Carbon::parse($doc->effective_date)->format('d-m-Y') : $doc->created_at->format('d-m-Y') }}
                            </p>
                        </div>

                        @if ($doc->file)
                            <a href="{{ route('documents.download', $doc) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-1.5 text-xs font-medium text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 transition">
                                <svg class="h-3.5 w-3.5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download Origineel
                            </a>
                        @endif
                    </div>

                    <!-- Answer Snippet -->
                    <div class="mt-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 p-4 border border-zinc-100 dark:border-zinc-800">
                        <div class="text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1 flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            Relevante Passageweergeving
                        </div>
                        <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                            {{ $item['matched_paragraph'] ?: $item['snippet'] }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-zinc-200 bg-white p-12 text-center dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-zinc-900 dark:text-white">Geen overeenkomende beleidsregels gevonden</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Probeer een andere zoekterm of vink "Toon ook historische versies" aan.
                    </p>
                </div>
            @endforelse
        </div>

    </div>
</x-layouts::app>
