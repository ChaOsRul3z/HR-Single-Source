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


<section class="mx-auto w-full max-w-3xl">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Documents') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('Upload policies, handbooks and contracts to the single source of truth') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
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

        <div class="mt-4 flex flex-col gap-2">
            @foreach ($files as $index => $file)
                <!-- Important: Ensure wire:key is completely unique -->
                <flux:file-item wire:key="file-item-{{ $index }}-{{ $file->getClientOriginalName() }}"
                    :heading="$file->getClientOriginalName()"
                    :size="$file->getSize()"
                    :invalid="$errors->has('files.'.$index)">

                    <x-slot name="actions">
                        <flux:file-item.remove wire:click="removeFile({{ $index }})"
                            aria-label="{{ 'Remove file: ' . $file->getClientOriginalName() }}" />
                    </x-slot>
                </flux:file-item>

                <!-- 2. Targeted item index error (This is where the duplicate hash error drops) -->
                <flux:error name="files.{{ $index }}" />
            @endforeach
        </div>

        @if ($files)
            <flux:button type="submit" variant="primary" class="mt-4">{{ __('Upload') }}</flux:button>
        @endif
    </form>

    <div class="mt-10">
        <livewire:documents-table />
    </div>
</section>
