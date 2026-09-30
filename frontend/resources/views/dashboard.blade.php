<x-layouts::app :title="__('Dashboard')">
    <div class="flex w-full flex-1 flex-col gap-8 font-sans antialiased text-zinc-900 dark:text-zinc-100">

        <!-- SD Worx Branded Hero Banner -->
                <div class="relative overflow-hidden rounded-3xl bg-[#00216B] p-8 text-white shadow-xl md:p-12 lg:p-16">
                    <!-- Modern fluid background ambient glow using SD Worx blue styling -->
                    <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full bg-blue-400/10 blur-3xl"></div>
                    <div class="absolute -left-20 -bottom-20 h-80 w-80 rounded-full bg-emerald-400/10 blur-3xl"></div>

                    <div class="relative z-10 max-w-2xl">
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-blue-200 backdrop-blur-md">
                            <span class="h-2 w-2 rounded-full bg-[#00D364] animate-pulse"></span> HR Single Source • SD Worx Ecosystem
                        </div>
                        <h1 class="mt-4 text-3xl font-extrabold tracking-tight md:text-5xl">Eén bron van waarheid voor al uw HR-beleid.</h1>
                        <p class="mt-4 text-base text-blue-100/90 md:text-lg leading-relaxed">Transformeer complexiteit in vertrouwen. Zoek, beheer en valideer documenten direct zonder verouderde versies.</p>

                        <!-- Search Bar -->
                        <form method="GET" action="{{ route('search') }}" class="mt-8 flex items-center gap-2 rounded-full bg-white p-2 shadow-2xl dark:bg-zinc-900 border border-white/20">
                            <div class="pl-4 text-zinc-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input name="q" placeholder="Zoek op beleid, vaderschapsverlof, contracten..."
                                   class="flex-1 border-0 bg-transparent px-2 py-2 text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-0 text-sm md:text-base">
                            <button class="rounded-full bg-[#008963] px-7 py-3 text-sm font-semibold text-white transition hover:bg-[#00B563] shadow-md">Zoek</button>
                        </form>
                    </div>
                </div>

        <!-- Metrics Grid -->
        <div class="grid gap-5 md:grid-cols-3">
            @foreach ([
                ['Actieve documenten', $activeCount, 'text-emerald-600 dark:text-emerald-400', 'bg-emerald-50 dark:bg-emerald-950/40'],
                ['Gearchiveerde versies', $archivedCount, 'text-zinc-600 dark:text-zinc-400', 'bg-zinc-100 dark:bg-zinc-800/50'],
                ['Laatst bijgewerkt', $lastUpdated ? \Illuminate\Support\Carbon::parse($lastUpdated)->format('d-m-Y') : '—', 'text-blue-600 dark:text-blue-400', 'bg-blue-50 dark:bg-blue-950/40'],
            ] as [$label, $value, $textColor, $bgColor])
                <div class="relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $label }}</p>
                        <div class="rounded-xl p-2.5 {{ $bgColor }}">
                            <div class="h-2 w-2 rounded-full {{ str_replace('text-', 'bg-', explode(' ', $textColor)[0]) }}"></div>
                        </div>
                    </div>
                    <p class="mt-4 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <!-- Navigation Cards -->
        <div class="grid gap-5 md:grid-cols-3">
            @foreach ([
                ['Document Uploaden', 'Voeg nieuwe documenten of revisies toe met automatische versiecontrole.', route('documents.upload'), 'M12 4v16m8-8H4'],
                ['HR Admin Portal', 'Centraal beheer van bestanden, afdelingen en toegangsrechten.', route('admin.documents'), 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['AI Beleid Zoeker', 'Vraag direct gerichte vragen over het actuele bedrijfsreglement.', route('search'), 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
            ] as [$title, $text, $url, $svgPath])
                <a href="{{ $url }}"
                   class="group relative flex flex-col justify-between rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-blue-600/40 hover:shadow-xl dark:border-zinc-800 dark:bg-zinc-900">
                   <div>
                       <div class="inline-flex rounded-xl bg-blue-50 p-3 text-blue-600 transition group-hover:bg-[#0F2243] group-hover:text-white dark:bg-blue-950/50 dark:text-blue-400">
                           <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $svgPath }}" />
                           </svg>
                       </div>
                       <h2 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-white">{{ $title }}</h2>
                       <p class="mt-2 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $text }}</p>
                   </div>
                   <div class="mt-6 flex items-center text-sm font-semibold text-blue-600 transition group-hover:translate-x-1 dark:text-blue-400">
                       Open module <span class="ml-1">→</span>
                   </div>
                </a>
            @endforeach
        </div>

        <!-- Recent Documents Table -->
        <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Recente documenten & versiebeheer</h2>
                <span class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50 px-3 py-1 rounded-full">Live Sync</span>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-wider text-zinc-400">
                        <tr>
                            <th class="py-3 font-semibold">Titel</th>
                            <th class="py-3 font-semibold">Type</th>
                            <th class="py-3 font-semibold">Grootte</th>
                            <th class="py-3 font-semibold">Geüpload</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @forelse ($recent as $d)
                            <tr class="transition hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="py-4 font-medium text-zinc-900 dark:text-white">{{ $d->original_name }}</td>
                                <td class="py-4">
                                    <flux:badge size="sm" color="blue">{{ strtoupper(pathinfo($d->original_name, PATHINFO_EXTENSION)) }}</flux:badge>
                                </td>
                                <td class="py-4 font-mono text-xs text-zinc-500">{{ $d->readable_size }}</td>
                                <td class="py-4 text-zinc-600 dark:text-zinc-300">{{ $d->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-zinc-500">
                                    Nog geen documenten geladen in het systeem.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts::app>
