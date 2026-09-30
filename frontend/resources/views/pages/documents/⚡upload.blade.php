<?php

use Flux\Flux;
use Illuminate\Http\UploadedFile;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Documents')]
    class extends Component {
    use WithFileUploads;

    /** @var array<int, UploadedFile> */
    #[Validate(['files.*' => 'file|mimes:pdf,doc,docx,txt|max:20480'])]
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
     * Store the queued files.
     */
    public function save(): void
    {
        $this->validate();

        foreach ($this->files as $file) {
            $file->store(path: 'documents');
        }

        $this->reset('files');

        Flux::toast(variant: 'success', text: __('Files uploaded.'));
    }
}; ?>

<section class="mx-auto w-full max-w-3xl">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Documents') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('Upload policies, handbooks and contracts to the single source of truth') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <form wire:submit="save">
        <flux:file-upload wire:model="files" multiple label="Upload files">
            <flux:file-upload.dropzone heading="Drop files here or click to browse" text="PDF, DOC, DOCX, TXT up to 20MB" with-progress />
        </flux:file-upload>

        <div class="mt-4 flex flex-col gap-2">
            @foreach ($files as $index => $file)
                <flux:file-item
                    wire:key="file-{{ $index }}"
                    :heading="$file->getClientOriginalName()"
                    :size="$file->getSize()"
                    :invalid="$errors->has('files.'.$index)"
                >
                    <x-slot name="actions">
                        <flux:file-item.remove wire:click="removeFile({{ $index }})" aria-label="{{ 'Remove file: '.$file->getClientOriginalName() }}" />
                    </x-slot>
                </flux:file-item>
            @endforeach
        </div>

        @if ($files)
            <flux:button type="submit" variant="primary" class="mt-4">{{ __('Upload') }}</flux:button>
        @endif
    </form>
</section>
