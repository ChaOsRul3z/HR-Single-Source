<?php

use App\Models\File; // Make sure you have this model
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
     * Store the queued files and save to the database.
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

        // 3. Pre-check files for duplicate hashes
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

        // 5. Safe Zone: Process unique files
        foreach ($this->files as $file) {
            $hash = hash_file('sha256', $file->getRealPath());
            $originalName = $file->getClientOriginalName();
            $filename = uniqid() . '_' . $originalName;

            $path = $file->storeAs('documents', $filename, 'public');

            File::create([
                'original_name' => $originalName,
                'storage_path'  => $path,
                'size'          => $file->getSize(),
                'mime_type'     => $file->getMimeType(),
                'file_hash'     => $hash,
            ]);
        }

        $this->reset('files');
        $this->dispatch('documents-uploaded');
        Flux::toast(variant: 'success', text: __('All files uploaded and saved successfully.'));
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

    <form wire:submit="save">
        <flux:file-upload wire:model="files" multiple label="Upload files" error:deep="false">
            <flux:file-upload.dropzone heading="Drop files here or click to browse"
                text="PDF, DOC, DOCX, XLS, XLSX up to 10MB (max 10 files)" with-progress />
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
