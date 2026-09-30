<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welkom · {{ config('app.name', 'HR Single Source') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen flex flex-col bg-brand-grey text-zinc-900 antialiased dark:bg-zinc-900 dark:text-zinc-100">

    {{-- Header --}}
    <header class="bg-white border-b border-zinc-200 dark:bg-zinc-950 dark:border-zinc-800">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <span class="flex size-9 items-center justify-center rounded-lg bg-brand-navy text-sm font-bold text-white dark:bg-accent dark:text-accent-foreground">HR</span>
                <span class="hidden font-semibold text-brand-navy sm:inline dark:text-white">{{ config('app.name', 'HR Single Source') }}</span>
            </a>

            <div class="flex items-center gap-3">

                @auth
                    @if (Route::has('dashboard'))
                        <flux:button :href="route('dashboard')" size="sm" icon="squares-2x2">Dashboard</flux:button>
                    @endif
                @else
                    @if (Route::has('login'))
                        <flux:button :href="route('login')" size="sm" variant="primary">Inloggen</flux:button>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1">
        {{-- Hero --}}
        <section class="bg-brand-navy text-white">
            <div class="mx-auto grid max-w-6xl items-center gap-12 px-6 py-20 lg:grid-cols-[1.2fr_1fr] lg:py-24">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white/80">
                        <span class="size-1.5 rounded-full bg-accent dark:bg-accent"></span>
                        HR Single Source
                    </span>

                    <h1 class="mt-6 text-4xl font-bold leading-tight tracking-tight sm:text-5xl">
                        Al je HR-documenten op één plek.
                    </h1>

                    <p class="mt-5 max-w-xl text-lg text-white/70">
                        Beleid, contracten en procedures: altijd de nieuwste versie, makkelijk te vinden en toegankelijk voor iedereen die ze nodig heeft.
                    </p>

                    <div class="mt-9 flex flex-wrap gap-3">
                        @auth
                            @if (Route::has('documents.index'))
                                <flux:button :href="route('documents.index')" variant="primary" icon-trailing="arrow-right">Documenten bekijken</flux:button>
                            @endif
                            @if (Route::has('dashboard'))
                                <a href="{{ route('dashboard') }}" class="inline-flex h-10 items-center rounded-lg border border-white/20 px-4 text-sm font-medium text-white transition hover:bg-white/10">Naar dashboard</a>
                            @endif
                        @else
                            @if (Route::has('login'))
                                <flux:button :href="route('login')" variant="primary" icon-trailing="arrow-right">Inloggen</flux:button>
                            @endif
                            @if (Route::has('documents.index'))
                                <a href="{{ route('documents.index') }}" class="inline-flex h-10 items-center rounded-lg border border-white/20 px-4 text-sm font-medium text-white transition hover:bg-white/10">Documenten bekijken</a>
                            @endif
                        @endauth
                    </div>
                </div>

                {{-- Mini preview of the document list, like on the dashboard --}}
                <div class="hidden lg:block" aria-hidden="true">
                    <div class="rounded-xl bg-white p-5 text-zinc-900 shadow-2xl shadow-black/30 dark:bg-zinc-900 dark:text-zinc-100">
                        <div class="mb-4 flex items-center justify-between">
                            <span class="text-sm font-semibold">Documenten</span>
                            <span class="rounded-md bg-brand-soft px-2 py-0.5 text-xs font-medium text-accent-content dark:bg-zinc-800 dark:text-accent">Actief</span>
                        </div>
                        @foreach ([
                            ['document-text', 'Arbeidsreglement', 'PDF'],
                            ['document-text', 'Onthaalbrochure', 'PDF'],
                            ['lock-closed', 'Loonbeleid 2026', 'DOCX'],
                        ] as [$icon, $name, $type])
                            <div class="flex items-center gap-3 border-t border-zinc-100 py-3 dark:border-zinc-800">
                                <span class="flex size-8 items-center justify-center rounded-lg bg-brand-soft text-accent dark:bg-zinc-800">
                                    <flux:icon :name="$icon" variant="micro" />
                                </span>
                                <span class="flex-1 text-sm font-medium">{{ $name }}</span>
                                <span class="text-xs text-zinc-400">{{ $type }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- Features --}}
        <section class="mx-auto grid max-w-6xl gap-4 px-6 py-16 md:grid-cols-3">
            @foreach ([
                ['document-duplicate', 'Eén bron van waarheid', 'Nooit meer zoeken in mailboxen en gedeelde mappen. Elk HR-document staat hier.'],
                ['arrow-path', 'Altijd up-to-date', 'Alleen actieve versies worden getoond, zodat iedereen met dezelfde informatie werkt.'],
                ['shield-check', 'Toegang per rol', 'Vertrouwelijke documenten zijn alleen zichtbaar voor wie ze nodig heeft.'],
            ] as [$icon, $title, $text])
                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-brand-soft text-accent dark:bg-zinc-900">
                        <flux:icon :name="$icon" class="size-5" />
                    </span>
                    <flux:heading size="lg" class="mt-4">{{ $title }}</flux:heading>
                    <flux:text class="mt-1.5">{{ $text }}</flux:text>
                </div>
            @endforeach
        </section>
    </main>

    <footer class="border-t border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
        <div class="mx-auto flex max-w-6xl flex-wrap justify-between gap-4 px-6 py-6 text-sm text-zinc-500 dark:text-zinc-400">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'HR Single Source') }}</span>
            <span>Vragen? Neem contact op met de HR-afdeling.</span>
        </div>
    </footer>

    @fluxScripts
</body>
</html>