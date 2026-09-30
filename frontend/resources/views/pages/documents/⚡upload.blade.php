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
        $this->dispatch('documents-uploaded');
        Flux::toast(variant: 'success', text: __('All files uploaded and saved successfully.'));
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
</section>

    <p class="text-center text-xs text-zinc-500">
        Identieke bestanden worden automatisch herkend en geweigerd, zodat er maar één bron van waarheid blijft.
    </p>
</div>
