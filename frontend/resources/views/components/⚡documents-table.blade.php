<?php

use App\Models\File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    /**
     * Documents passed in by the parent. When null, the table queries and paginates them itself.
     *
     * @var Collection<int, File>|null
     */
    public ?Collection $documents = null;

    #[Url(as: 'q')]
    public string $search = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    /**
     * Sort by the given column, toggling the direction when it is already active.
     */
    public function sort(string $column): void
    {
        if (! in_array($column, ['original_name', 'size', 'created_at'], true)) {
            return;
        }

        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[On('documents-uploaded')]
    public function refreshDocuments(): void
    {
        $this->resetPage();
    }

    /**
     * @return Collection<int, File>|LengthAwarePaginator<int, File>
     */
    #[Computed]
    public function rows(): Collection|LengthAwarePaginator
    {
        if ($this->documents !== null) {
            return $this->documents;
        }

        return File::query()
            ->when($this->search, fn ($query) => $query->where('original_name', 'like', '%'.$this->search.'%'))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    /**
     * The badge color for a file extension.
     */
    public function extensionColor(string $extension): string
    {
        return match ($extension) {
            'pdf' => 'red',
            'doc', 'docx' => 'blue',
            'xls', 'xlsx' => 'green',
            default => 'zinc',
        };
    }
};
?>

<div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
    <div class="flex flex-col gap-4 border-b border-zinc-100 pb-4 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-800">
        <div>
            <flux:heading size="lg">{{ $documents !== null ? __('Recente documenten') : __('Alle documenten') }}</flux:heading>
            @if ($documents === null)
                <flux:text>{{ trans_choice(':count document in de bron|:count documenten in de bron', $this->rows->total()) }}</flux:text>
            @endif
        </div>

        <div class="flex items-center gap-2">
            @if ($documents === null)
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" size="sm" :placeholder="__('Zoek op bestandsnaam...')" clearable class="sm:w-64" />
            @else
                <flux:button :href="route('documents.upload')" icon-trailing="arrow-right" size="sm" variant="primary" wire:navigate>
                    {{ __('Bekijk alles') }}
                </flux:button>
            @endif
        </div>
    </div>

    <flux:table :paginate="$documents === null ? $this->rows : null" class="mt-2">
        <flux:table.columns>
            <flux:table.column :sortable="$documents === null" :sorted="$sortBy === 'original_name'" :direction="$sortDirection" wire:click="sort('original_name')">{{ __('Document') }}</flux:table.column>
            <flux:table.column>{{ __('Type') }}</flux:table.column>
            <flux:table.column :sortable="$documents === null" :sorted="$sortBy === 'size'" :direction="$sortDirection" wire:click="sort('size')">{{ __('Grootte') }}</flux:table.column>
            <flux:table.column :sortable="$documents === null" :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">{{ __('Geüpload') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->rows as $document)
                @php($extension = strtolower(pathinfo($document->original_name, PATHINFO_EXTENSION)))

                <flux:table.row :key="$document->id">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-white/10">
                                <flux:icon name="document-text" variant="mini" class="text-zinc-500 dark:text-zinc-300" />
                            </div>
                            <span class="max-w-72 truncate font-medium text-zinc-900 dark:text-white">{{ $document->original_name }}</span>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" :color="$this->extensionColor($extension)" inset="top bottom">{{ strtoupper($extension) }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="tabular-nums">{{ $document->readable_size }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:tooltip :content="$document->created_at->format('d-m-Y H:i')">
                            <span>{{ $document->created_at->diffForHumans() }}</span>
                        </flux:tooltip>
                    </flux:table.cell>

                    <flux:table.cell align="end">
                        <flux:button
                            :href="Storage::disk('public')->url($document->storage_path)"
                            target="_blank"
                            icon="arrow-top-right-on-square"
                            size="sm"
                            variant="ghost"
                            inset="top bottom"
                            :aria-label="__('Open :name', ['name' => $document->original_name])"
                        />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">
                        <div class="flex flex-col items-center gap-2 py-10 text-center">
                            <flux:icon name="folder-open" class="text-zinc-400" />
                            <flux:text>{{ $search ? __('Geen documenten gevonden voor ":search".', ['search' => $search]) : __('Nog geen documenten geladen in het systeem.') }}</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
