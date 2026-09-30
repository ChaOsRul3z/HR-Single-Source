<x-layouts::app :title="__('HR Admin Portal - Documentbeheer')">
    <div class="flex w-full flex-1 flex-col gap-8 font-sans antialiased text-zinc-900 dark:text-zinc-100">

        <!-- Header -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-950/60 dark:text-blue-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span> HR Admin Console
                </div>
                <h1 class="mt-2 text-2xl font-bold tracking-tight md:text-3xl text-zinc-900 dark:text-white">Centraal Document- & Versiebeheer</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Beheer de enkele bron van waarheid (Single Source of Truth), bekijk versies en beheer archiveringsstatussen.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('documents.upload') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#008963] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#00B563] transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nieuwe Versie Uploaden
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.documents') }}" class="flex flex-wrap items-center gap-4 rounded-2xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 shadow-sm">
            <div class="flex items-center gap-2">
                <label for="department" class="text-xs font-semibold uppercase text-zinc-500">Afdeling:</label>
                <select name="department" id="department" onchange="this.form.submit()" class="rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    <option value="">Alle afdelingen</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept }}" {{ $selectedDepartment === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label for="status" class="text-xs font-semibold uppercase text-zinc-500">Status:</label>
                <select name="status" id="status" onchange="this.form.submit()" class="rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    <option value="">Alle statussen</option>
                    <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>Actief (Single Source)</option>
                    <option value="archived" {{ $selectedStatus === 'archived' ? 'selected' : '' }}>Gearchiveerd (Historisch)</option>
                </select>
            </div>

            @if ($selectedDepartment || $selectedStatus)
                <a href="{{ route('admin.documents') }}" class="text-xs text-zinc-500 hover:text-zinc-800 underline ml-auto">Filters wissen</a>
            @endif
        </form>

        <!-- Document List Table -->
        <div class="rounded-2xl border border-zinc-200/80 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-50/60 dark:bg-zinc-800/40 text-xs uppercase tracking-wider text-zinc-400 border-b border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <th class="py-3.5 px-6 font-semibold">Titel & Code</th>
                            <th class="py-3.5 px-4 font-semibold">Afdeling</th>
                            <th class="py-3.5 px-4 font-semibold">Versie</th>
                            <th class="py-3.5 px-4 font-semibold">Status</th>
                            <th class="py-3.5 px-4 font-semibold">Toegang</th>
                            <th class="py-3.5 px-4 font-semibold">Laatst Bijgewerkt</th>
                            <th class="py-3.5 px-6 font-semibold text-right">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @forelse ($documents as $doc)
                            <tr class="transition hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 {{ $doc->isArchived() ? 'opacity-60 bg-zinc-50/20' : '' }}">
                                <td class="py-4 px-6">
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $doc->title }}</div>
                                    <div class="text-xs text-zinc-400 font-mono">{{ $doc->document_code }}</div>
                                </td>
                                <td class="py-4 px-4 text-zinc-600 dark:text-zinc-300">
                                    <span class="inline-flex items-center rounded-md bg-zinc-100 dark:bg-zinc-800 px-2 py-1 text-xs font-medium text-zinc-600 dark:text-zinc-300">
                                        {{ $doc->department }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-mono text-xs">
                                    <span class="rounded bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 font-semibold text-blue-700 dark:text-blue-300">
                                        v{{ $doc->version }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    @if ($doc->isActive())
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Actief (Single Source)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-zinc-400"></span> Gearchiveerd
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if ($doc->is_restricted)
                                        <span class="inline-flex items-center rounded-md bg-amber-50 dark:bg-amber-950/50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:text-amber-300">
                                            HR / Admin Vertrouwelijk
                                        </span>
                                    @else
                                        <span class="text-xs text-zinc-500">Publiek</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-xs text-zinc-500">
                                    {{ $doc->updated_at->format('d-m-Y H:i') }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($doc->file)
                                            <a href="{{ route('documents.download', $doc) }}" class="rounded-lg p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-zinc-100 dark:hover:bg-zinc-800" title="Bestand downloaden">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                        @endif

                                        <form method="POST" action="{{ route('documents.toggle-status', $doc) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium {{ $doc->isActive() ? 'text-amber-600 hover:text-amber-700' : 'text-emerald-600 hover:text-emerald-700' }}">
                                                {{ $doc->isActive() ? 'Archiveer' : 'Heractiveer' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-zinc-500">
                                    Geen documenten gevonden die voldoen aan de zoekcriteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts::app>
