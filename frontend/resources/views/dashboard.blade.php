<x-layouts::app :title="__('Dashboard')">
    <div class="flex w-full flex-1 flex-col gap-8">

        <!-- SD Worx Hero Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-[#0B1D3A] p-8 text-white shadow-xl md:p-12">
            <!-- Decorative background glow for modern tech feel -->
            <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-blue-600/20 blur-3xl"></div>

            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-blue-200 backdrop-md">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span> SD Worx Ecosystem Hub
                </div>
                <h1 class="mt-4 text-3xl font-bold tracking-tight md:text-4xl lg:text-5xl">Eén plek voor het actuele HR-beleid.</h1>
                <p class="mt-3 max-w-xl text-base text-blue-100/80 md:text-lg">Zoek, beheer en upload beleid direct vanuit de centrale bron van waarheid zonder versieconflicten.</p>

                <form method="GET" action="{{ route('search') }}" class="mt-6 flex max-w-xl items-center gap-2 rounded-full bg-white p-2 shadow-lg">
                    <input name="q" placeholder="Zoek bv. vaderschapsverlof, thuiswerk..."
                           class="flex-1 border-0 bg-transparent px-4 py-2 text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-0">
                    <button class="rounded-full bg-[#0B1D3A] px-6 py-3 text-sm font-medium text-white transition hover:bg-blue-900">Zoek beleid</button>
                </form>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid gap-5 md:grid-cols-3">
            @foreach ([
                ['Actieve documenten', $activeCount ?? 2, 'text-emerald-600 dark:text-emerald-400', 'bg-emerald-50 dark:bg-emerald-950/30'],
                ['Gearchiveerde versies', $archivedCount ?? 1, 'text-zinc-600 dark:text-zinc-400', 'bg-zinc-100 dark:bg-zinc-800/50'],
                ['Laatst bijgewerkt', isset($lastUpdated) && $lastUpdated ? \Illuminate\Support\Carbon::parse($lastUpdated)->format('d-m-Y') : '—', 'text-blue-600 dark:text-blue-400', 'bg-blue-50 dark:bg-blue-950/30'],
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

        <!-- Action Shortcuts Grid -->
        <div class="grid gap-5 md:grid-cols-3">
            @foreach ([
                ['Uploaden', 'Nieuwe documenten of versies toevoegen met automatische versiecontrole.', route('documents.upload'), 'M12 4v16m8-8H4'],
                ['HR Admin Portal', 'Beheer centrale bestanden, rechten en goedkeuringen.', route('admin.documents'), 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['Slim Zoeken', 'Vraag direct iets over het actuele HR-beleid via semantische zoekopdrachten.', route('search'), 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
            ] as [$title, $text, $url, $svgPath])
                <a href="{{ $url }}"
                   class="group relative flex flex-col justify-between rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-blue-600/40 hover:shadow-xl dark:border-zinc-800 dark:bg-zinc-900">
                   <div>
                       <div class="inline-flex rounded-xl bg-blue-50 p-3 text-blue-600 transition group-hover:bg-blue-600 group-hover:text-white dark:bg-blue-950/50 dark:text-blue-400">
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

        <!-- Recent Documents Table Card -->
        <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between pb-4">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Recente documenten & versiegeschiedenis</h2>
                <span class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Live Repository</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-zinc-100 text-xs uppercase tracking-wider text-zinc-400 dark:border-zinc-800">
                        <tr>
                            <th class="py-3 font-semibold">Titel</th>
                            <th class="py-3 font-semibold">Afdeling</th>
                            <th class="py-3 font-semibold">Versie</th>
                            <th class="py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @forelse ($recent ?? [] as $d)
                            <tr class="transition hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 {{ $d->status === 'archived' ? 'opacity-50 grayscale-[30%]' : '' }}">
                                <td class="py-4 font-medium text-zinc-900 dark:text-white">{{ $d->title }}</td>
                                <td class="py-4 text-zinc-600 dark:text-zinc-300">{{ $d->department }}</td>
                                <td class="py-4 font-mono text-xs text-zinc-500">v{{ $d->version }}</td>
                                <td class="py-4">
                                    <flux:badge color="{{ $d->status === 'active' ? 'green' : 'zinc' }}">
                                        {{ $d->status === 'active' ? 'Actief' : 'Gearchiveerd' }}
                                    </flux:badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-zinc-500">
                                    Nog geen documenten geüpload. Gebruik de upload-module om te beginnen.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts::app>
