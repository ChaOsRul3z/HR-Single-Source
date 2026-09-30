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

        foreach ($this->files as $file) {
            $originalName = $file->getClientOriginalName();
            $filename = uniqid() . '_' . $originalName;

            // Store file in public disk
            $path = $file->storeAs('documents', $filename, 'public');

            // Save record to the database
            File::create([
                'name' => $originalName,
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        $this->reset('files');

        Flux::toast(variant: 'success', text: __('Files uploaded and saved successfully.'));
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
        <flux:file-upload wire:model="files" multiple label="Upload files">
            <flux:file-upload.dropzone heading="Drop files here or click to browse"
                text="PDF, DOC, DOCX, XLS, XLSX up to 10MB (max 10 files)" with-progress />
        </flux:file-upload>

        <flux:error name="files" />

        <div class="mt-4 flex flex-col gap-2">
            @foreach ($files as $index => $file)
                <flux:file-item wire:key="file-{{ $index }}" :heading="$file->getClientOriginalName()"
                    :size="$file->getSize()" :invalid="$errors->has('files.'.$index)">
                    <x-slot name="actions">
                        <flux:file-item.remove wire:click="removeFile({{ $index }})"
                            aria-label="{{ 'Remove file: ' . $file->getClientOriginalName() }}" />
                    </x-slot>
                </flux:file-item>

                <flux:error name="files.{{ $index }}" />
            @endforeach
        </div>

        @if ($files)
            <flux:button type="submit" variant="primary" class="mt-4">{{ __('Upload') }}</flux:button>
        @endif
    </form>
</section>
