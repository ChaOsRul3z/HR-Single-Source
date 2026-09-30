<?php

use App\Models\Document;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
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
     * @var Collection<int, Document>|null
     */
    public ?Collection $documents = null;

    #[Url(as: 'q')]
    public string $search = '';

    public string $sortBy = 'updated_at';

    public string $sortDirection = 'desc';

    /**
     * Sort by the given column, toggling the direction when it is already active.
     */
    public function sort(string $column): void
    {
        if (! in_array($column, ['title', 'department', 'version', 'status', 'updated_at'], true)) {
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
     * @return Collection<int, Document>|LengthAwarePaginator<int, Document>
     */
    #[Computed]
    public function rows(): Collection|LengthAwarePaginator
    {
        if ($this->documents !== null) {
            return $this->documents->loadMissing('file');
        }

        return Document::query()
            ->accessibleBy(Auth::user())
            ->with('file')
            ->when($this->search, fn ($query) => $query->where(fn ($query) => $query
                ->where('title', 'like', '%'.$this->search.'%')
                ->orWhere('department', 'like', '%'.$this->search.'%')
                ->orWhere('document_code', 'like', '%'.$this->search.'%')))
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
            <flux:heading size="lg">{{ $documents !== null ? __('Recent documents') : __('All documents') }}</flux:heading>
            @if ($documents === null)
                <flux:text>{{ trans_choice(':count document in the source|:count documents in the source', $this->rows->total()) }}</flux:text>
            @endif
        </div>

        <div class="flex items-center gap-2">
            @if ($documents === null)
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" size="sm" :placeholder="__('Search by title, department or code...')" clearable class="sm:w-64" />
            @else
                <flux:button :href="route('documents.upload')" icon-trailing="arrow-right" size="sm" variant="primary" wire:navigate>
                    {{ __('View all') }}
                </flux:button>
            @endif
        </div>
    </div>

    <flux:table :paginate="$documents === null ? $this->rows : null" class="mt-2">
        <flux:table.columns>
            <flux:table.column :sortable="$documents === null" :sorted="$sortBy === 'title'" :direction="$sortDirection" wire:click="sort('title')">{{ __('Title') }}</flux:table.column>
            <flux:table.column :sortable="$documents === null" :sorted="$sortBy === 'department'" :direction="$sortDirection" wire:click="sort('department')">{{ __('Department') }}</flux:table.column>
            <flux:table.column :sortable="$documents === null" :sorted="$sortBy === 'version'" :direction="$sortDirection" wire:click="sort('version')">{{ __('Version') }}</flux:table.column>
            <flux:table.column :sortable="$documents === null" :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('File') }}</flux:table.column>
            <flux:table.column :sortable="$documents === null" :sorted="$sortBy === 'updated_at'" :direction="$sortDirection" wire:click="sort('updated_at')">{{ __('Updated') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->rows as $document)
                @php($extension = $document->file ? strtolower(pathinfo($document->file->original_name, PATHINFO_EXTENSION)) : null)

                <flux:table.row :key="$document->id" @class(['opacity-60' => $document->isArchived()])>
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-white/10">
                                <flux:icon :name="$document->is_restricted ? 'lock-closed' : 'document-text'" variant="mini" class="text-zinc-500 dark:text-zinc-300" />
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('documents.show', $document) }}" class="block max-w-64 truncate font-medium text-zinc-900 hover:underline dark:text-white">{{ $document->title }}</a>
                                <span class="font-mono text-xs text-zinc-500">{{ $document->document_code }}</span>
                            </div>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>{{ $document->department }}</flux:table.cell>

                    <flux:table.cell class="font-mono text-xs">v{{ $document->version }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" :color="$document->isActive() ? 'green' : 'zinc'" inset="top bottom">
                            {{ $document->isActive() ? __('Active') : __('Archived') }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        @if ($document->file)
                            <div class="flex items-center gap-2">
                                <flux:badge size="sm" :color="$this->extensionColor($extension)" inset="top bottom">{{ strtoupper($extension) }}</flux:badge>
                                <span class="tabular-nums text-zinc-500">{{ $document->file->readable_size }}</span>
                            </div>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:tooltip :content="$document->updated_at->format('d-m-Y H:i')">
                            <span>{{ $document->updated_at->diffForHumans() }}</span>
                        </flux:tooltip>
                    </flux:table.cell>

                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-1">
                            <flux:button :href="route('documents.show', $document)" icon="eye" size="sm" variant="ghost" inset="top bottom" :aria-label="__('View :title', ['title' => $document->title])" />
                            @if ($document->file)
                                <flux:button :href="route('documents.download', $document)" icon="arrow-down-tray" size="sm" variant="ghost" inset="top bottom" :aria-label="__('Download :title', ['title' => $document->title])" />
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7">
                        <div class="flex flex-col items-center gap-2 py-10 text-center">
                            <flux:icon name="folder-open" class="text-zinc-400" />
                            <flux:text>{{ $search ? __('No documents found for ":search".', ['search' => $search]) : __('No documents in the system yet.') }}</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
