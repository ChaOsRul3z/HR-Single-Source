<?php

use App\Models\Document;
use App\Models\File;
use Flux\Flux;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Documents')]
class extends Component {
    use WithFileUploads;

    /** @var array<int, UploadedFile> */
    public array $files = [];

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
     * Clear the upload queue and errors when the modal closes.
     */
    public function clearUpload(): void
    {
        $this->reset('files');
        $this->resetErrorBag();
    }

    /**
     * Store the queued files and save to the database using the Document model.
     */
    public function save(): void
    {
        // 1. Wipe away ALL old validation errors from prior attempts
        $this->resetErrorBag();

        // 2. Run basic structural validation
        $this->validate(
            rules: [
                'files' => ['required', 'array', 'min:1', 'max:10'],
                'files.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx', 'min:1', 'max:10240'],
            ],
            messages: [
                'files.required' => __('Select at least one file.'),
                'files.max' => __('You can upload up to 10 files at once.'),
                'files.*.mimes' => __('Only PDF, DOC, DOCX, XLS and XLSX files are allowed.'),
                'files.*.min' => __('The file is empty.'),
                'files.*.max' => __('Each file may not be larger than 10MB.'),
            ],
        );

        // 3. Pre-check files for duplicate hashes using the Document model
        foreach ($this->files as $index => $file) {
            $hash = hash_file('sha256', $file->getRealPath());

            if (File::where('file_hash', $hash)->exists()) {
                // Attach error ONLY to this specific index
                $this->addError("files.{$index}", __('This exact file document has already been uploaded previously.'));
            }
        }

        // 4. If errors exist, stop and let Livewire re-render
        if ($this->getErrorBag()->isNotEmpty()) {
            Flux::toast(variant: 'danger', text: __('Upload halted: Duplicate files detected.'));
            return;
        }

        // 5. Safe Zone: Process unique files and map to Document attributes
        $user = auth()->user();

        foreach ($this->files as $file) {
            $hash = hash_file('sha256', $file->getRealPath());
            $originalName = $file->getClientOriginalName();
            $filename = uniqid() . '_' . $originalName;

            $path = $file->storeAs('documents', $filename, 'public');

            // Automatically extract a clean slug-like document code from the name
            $documentCode = \Illuminate\Support\Str::slug(pathinfo($originalName, PATHINFO_FILENAME));

            // Check if this document code already exists to handle auto-versioning
            $latestVersion = Document::where('document_code', $documentCode)->max('version') ?? 0;

            $storedFile = File::create([
                'original_name' => $originalName,
                'storage_path'  => $path,
                'size'          => $file->getSize(),
                'mime_type'     => $file->getMimeType(),
                'file_hash'     => $hash,
            ]);

            Document::create([
                'file_id'        => $storedFile->id,
                'title'          => pathinfo($originalName, PATHINFO_FILENAME),
                'document_code'  => $documentCode,
                'version'        => $latestVersion + 1,
                'status'         => 'active', // New uploads become the active single source of truth
                'department'     => 'HR',     // Default department fallback
                'is_restricted'  => false,
                'user_id'        => $user?->id,
                'effective_date' => now()->toDateString(),
            ]);
        }

        $this->reset('files');
        $this->dispatch('documents-uploaded');
        Flux::modal('upload-documents')->close();
        Flux::toast(variant: 'success', text: __('All files uploaded and saved successfully as the active source of truth.'));
    }
}; ?>


<section class="mx-auto w-full max-w-5xl">
    <div class="relative mb-6 w-full">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <flux:heading size="xl" level="1">{{ __('Documents') }}</flux:heading>
                <flux:subheading size="lg">
                    {{ __('Upload policies, handbooks and contracts to the single source of truth') }}
                </flux:subheading>
            </div>

            <flux:modal.trigger name="upload-documents">
                <flux:button variant="primary" icon="arrow-up-tray">{{ __('Upload documents') }}</flux:button>
            </flux:modal.trigger>
        </div>

        <flux:separator variant="subtle" class="mt-6" />
    </div>

    <livewire:documents-table />

    <flux:modal name="upload-documents" class="w-full md:max-w-xl" @close="clearUpload">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Upload documents') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Duplicate files are detected automatically.') }}</flux:text>
            </div>

            <flux:file-upload wire:model="files" multiple :label="__('Upload files')" error:deep="false">
                <flux:file-upload.dropzone :heading="__('Drop files here or click to browse')"
                    :text="__('PDF, DOC, DOCX, XLS, XLSX up to 10MB (max 10 files)')" with-progress />
            </flux:file-upload>

            @if ($files)
                <div class="flex max-h-72 flex-col gap-2 overflow-y-auto">
                    @foreach ($files as $index => $file)
                        <flux:file-item wire:key="file-item-{{ $index }}-{{ $file->getClientOriginalName() }}"
                            :heading="$file->getClientOriginalName()"
                            :size="$file->getSize()"
                            :invalid="$errors->has('files.'.$index)">
                            <x-slot name="actions">
                                <flux:file-item.remove wire:click="removeFile({{ $index }})"
                                    :aria-label="__('Remove file: :name', ['name' => $file->getClientOriginalName()])" />
                            </x-slot>
                        </flux:file-item>

                        <flux:error name="files.{{ $index }}" />
                    @endforeach
                </div>
            @endif

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary" :disabled="! $files">
                    {{ __('Upload') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</section>
