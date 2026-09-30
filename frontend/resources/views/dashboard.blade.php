<x-layouts::app :title="__('Dashboard')">
    <div class="flex w-full flex-1 flex-col gap-8">

        <div class="rounded-2xl bg-brand-navy p-8 text-white">
            <p class="text-sm uppercase tracking-widest opacity-70">HR Single Source</p>
            <h1 class="mt-2 text-3xl font-semibold md:text-4xl">Eén plek voor het actuele HR-beleid.</h1>
            <p class="mt-3 max-w-xl opacity-80">Zoek, beheer en upload beleid zonder oude versies tegen te komen.</p>
            <form method="GET" action="{{ route('search') }}" class="mt-6 flex max-w-xl gap-2">
                <input name="q" placeholder="Zoek bv. vaderschapsverlof"
                       class="flex-1 rounded-full border-0 bg-white px-5 py-3 text-zinc-900 placeholder-zinc-400 focus:ring-2 focus:ring-white/60">
                <button class="rounded-full bg-white px-6 py-3 font-medium text-brand-navy">Zoek</button>
            </form>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            @foreach ([
                ['Actieve documenten', $activeCount ?? 2],
                ['Gearchiveerde versies', $archivedCount ?? 1],
                ['Laatst bijgewerkt', isset($lastUpdated) && $lastUpdated ? \Illuminate\Support\Carbon::parse($lastUpdated)->format('d-m-Y') : '—'],
            ] as [$label, $value])
                <div class="rounded-2xl bg-brand-soft p-6 dark:bg-zinc-800">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $label }}</p>
                    <p class="mt-2 text-3xl font-semibold text-brand-navy dark:text-white">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            @foreach ([
                ['Uploaden', 'Nieuwe documenten of versies toevoegen.', route('documents.upload')],
                ['HR Admin Portal', 'Documenten en versies beheren.', route('admin.documents')],
                ['Zoeken', 'Vraag iets over het HR-beleid.', route('search')],
            ] as [$title, $text, $url])
                <a href="{{ $url }}"
                   class="rounded-2xl border border-zinc-200 bg-white p-6 transition hover:-translate-y-0.5 hover:shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-brand-navy dark:text-white">{{ $title }}</h2>
                    <p class="mt-2 text-sm text-zinc-500">{{ $text }}</p>
                    <span class="mt-4 inline-block text-sm font-medium text-accent">Open →</span>
                </a>
            @endforeach
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="mb-4 text-lg font-semibold text-brand-navy dark:text-white">Recente documenten</h2>
            <table class="w-full text-left text-sm">
                <thead class="text-zinc-500">
                    <tr class="border-b">
                        <th class="py-2 font-medium">Titel</th>
                        <th class="font-medium">Afdeling</th>
                        <th class="font-medium">Versie</th>
                        <th class="font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recent ?? [] as $d)
                        <tr class="border-b last:border-0 {{ $d->status === 'archived' ? 'opacity-50' : '' }}">
                            <td class="py-3 font-medium">{{ $d->title }}</td>
                            <td>{{ $d->department }}</td>
                            <td>v{{ $d->version }}</td>
                            <td>
                                <flux:badge color="{{ $d->status === 'active' ? 'green' : 'zinc' }}">
                                    {{ $d->status === 'active' ? 'Actief' : 'Gearchiveerd' }}
                                </flux:badge>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-zinc-500">Nog geen documenten.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts::app>