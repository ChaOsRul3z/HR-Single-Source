<?php

use App\Models\File;
use App\Services\DocumentIngestionService;
use Flux\Flux;
use Illuminate\Http\UploadedFile;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Documents')]
    class extends Component {
    use WithFileUploads;

    /** @var array<int, UploadedFile> */
    public array $files = [];

    public string $department = 'HR';

    public bool $is_restricted = false;

    /**
     * Remove a file from the upload queue.
     */
    public function removeFile(int $index): void
    {
        $this->files[$index]->delete();
        unset($this->files[$index]);
        $this->files = array_values($this->files);
    }

    /**
     * Store the queued files, extract intelligence, and save to the single source repository.
     */
    public function save(): void
    {
        $this->resetErrorBag();

        $this->validate(
            rules: [
                'files' => ['required', 'array', 'min:1', 'max:10'],
                'files.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,txt,md', 'min:1', 'max:10240'],
                'department' => ['required', 'string', 'max:50'],
            ],
            messages: [
                'files.required' => __('Select at least one file.'),
                'files.max' => __('You can upload up to 10 files at once.'),
                'files.*.mimes' => __('Only PDF, DOC, DOCX, XLS, XLSX, TXT and MD files are allowed.'),
                'files.*.min' => __('The file is empty.'),
                'files.*.max' => __('Each file may not be larger than 10MB.'),
            ],
        );

        // Pre-check files for duplicate hashes
        foreach ($this->files as $index => $file) {
            $hash = hash_file('sha256', $file->getRealPath());

            if (File::where('file_hash', $hash)->exists()) {
                $this->addError("files.{$index}", __('This exact file document has already been uploaded previously.'));
            }
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            Flux::toast(variant: 'danger', text: __('Upload halted: Duplicate files detected.'));
            return;
        }

        /** @var DocumentIngestionService $ingestionService */
        $ingestionService = app(DocumentIngestionService::class);
        $user = auth()->user();

        $count = 0;
        foreach ($this->files as $file) {
            $ingestionService->ingestUploadedFile(
                $file,
                $user,
                [
                    'department' => $this->department,
                    'is_restricted' => $this->is_restricted,
                ]
            );
            $count++;
        }

        $this->reset('files');
<<<<<<< HEAD
        Flux::toast(variant: 'success', text: __(':count document(s) ingested into Single Source of Truth.', ['count' => $count]));
=======
        $this->dispatch('documents-uploaded');
        Flux::toast(variant: 'success', text: __('All files uploaded and saved successfully.'));
>>>>>>> c41d43415be9c02915e96f5df0ba22e0adfa31d1
    }

}; ?>


<div class="mx-auto flex w-full max-w-4xl flex-1 flex-col gap-8 font-sans antialiased text-zinc-900 dark:text-zinc-100">

    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-3xl bg-[#00216B] p-8 text-white shadow-xl md:p-10">
        <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-blue-400/10 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>

        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-blue-200 backdrop-blur-md">
                <span class="h-2 w-2 animate-pulse rounded-full bg-[#00D364]"></span>
                HR Single Source
            </div>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight md:text-4xl">Document uploaden</h1>
            <p class="mt-3 max-w-xl text-base leading-relaxed text-blue-100/90">
                Voeg beleid, handboeken en contracten toe aan de centrale bron van waarheid.
            </p>
        </div>
    </div>

<<<<<<< HEAD
    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="department" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Afdeling / Department</label>
                <select id="department" wire:model="department" class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">
                    <option value="HR">HR (Human Resources)</option>
                    <option value="Legal">Legal & Compliance</option>
                    <option value="Finance">Finance & Payroll</option>
                    <option value="Operations">Operations & IT</option>
                </select>
            </div>
            <div class="flex items-center pt-6">
                <label class="relative flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="is_restricted" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Vertrouwelijk document (HR / Admin restrictie)
                    </span>
                </label>
            </div>
        </div>

        <flux:file-upload wire:model="files" multiple label="Upload files" error:deep="false">
            <flux:file-upload.dropzone heading="Drop files here or click to browse"
                text="PDF, DOC, DOCX, XLS, XLSX, TXT, MD up to 10MB (max 10 files)" with-progress />
        </flux:file-upload>
=======
    {{-- Upload --}}
    <form wire:submit="save"
          class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
>>>>>>> 428235c8af0f3fb8c52d30a59a3af5eb4d3f1a12

        <div x-data="{ over: false }"
             @dragover.prevent="over = true"
             @dragleave.prevent="over = false"
             @drop="over = false"
             :class="over ? 'border-blue-600 bg-blue-50 dark:bg-blue-950/30' : 'border-zinc-300 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-950'"
             class="relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed px-6 py-14 text-center transition">

            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L7 9m5-5 5 5M5 14v5h14v-5" />
                </svg>
            </div>

            <p class="mt-4 text-base font-semibold text-zinc-900 dark:text-white">
                Sleep bestanden hierheen of klik om te bladeren
            </p>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                PDF, DOC, DOCX, XLS en XLSX tot 10 MB (maximaal 10 bestanden)
            </p>

            <div wire:loading wire:target="files" class="mt-4 text-sm font-medium text-blue-600 dark:text-blue-400">
                Bestanden worden geladen…
            </div>

            <input type="file" wire:model="files" multiple
                   accept=".pdf,.doc,.docx,.xls,.xlsx"
                   class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                   aria-label="Kies bestanden om te uploaden">
        </div>

        @error('files')
            <p class="mt-3 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror

        {{-- Wachtrij --}}
        @if (count($files) > 0)
            <div class="mt-6 space-y-3">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-400">
                    Klaar om te uploaden ({{ count($files) }})
                </h2>

                @foreach ($files as $index => $file)
                    <div wire:key="file-item-{{ $index }}-{{ $file->getClientOriginalName() }}">
                        <div class="flex items-center gap-3 rounded-xl border p-3 {{ $errors->has('files.'.$index) ? 'border-red-300 bg-red-50 dark:border-red-900 dark:bg-red-950/30' : 'border-zinc-200 dark:border-zinc-800' }}">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 3.75h7l5 5v11.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2ZM14 4v5h5M9 14h6M9 17h6" />
                                </svg>
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $file->getClientOriginalName() }}</p>
                                <p class="text-xs text-zinc-500">{{ \Illuminate\Support\Number::fileSize($file->getSize()) }}</p>
                            </div>

                            <button type="button" wire:click="removeFile({{ $index }})"
                                    class="rounded-lg p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-red-600 dark:hover:bg-zinc-800"
                                    aria-label="Verwijder {{ $file->getClientOriginalName() }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        @error('files.'.$index)
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-full bg-[#008963] px-7 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-[#00B563] disabled:opacity-60"
                        wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Uploaden</span>
                    <span wire:loading wire:target="save">Bezig…</span>
                </button>
            </div>
        @endif
    </form>
</section>

    <p class="text-center text-xs text-zinc-500">
        Identieke bestanden worden automatisch herkend en geweigerd, zodat er maar één bron van waarheid blijft.
    </p>
</div>