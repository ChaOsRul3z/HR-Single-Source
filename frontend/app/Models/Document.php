<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $file_id
 * @property int|null $user_id
 * @property string $title
 * @property string $document_code
 * @property string $department
 * @property int $version
 * @property string $status
 * @property bool $is_restricted
 * @property string|null $effective_date
 * @property string|null $summary
 * @property string|null $extracted_text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read File|null $file
 * @property-read User|null $user
 */
class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'user_id',
        'title',
        'document_code',
        'department',
        'version',
        'status',
        'is_restricted',
        'effective_date',
        'summary',
        'extracted_text',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'is_restricted' => 'boolean',
            'effective_date' => 'date',
        ];
    }

    /**
     * Associated stored file.
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    /**
     * Uploader / Author.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: only active single-source-of-truth documents.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: only archived legacy documents.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope: filter out confidential HR files if user is a regular employee.
     */
    public function scopeAccessibleBy(Builder $query, ?User $user): Builder
    {
        if ($user && $user->canAccessRestrictedDocuments()) {
            return $query;
        }

        return $query->where('is_restricted', false);
    }

    /**
     * Check if document is the active single source of truth.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if document is an archived version.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Archive this document version.
     */
    public function archive(): bool
    {
        $this->status = 'archived';
        return $this->save();
    }

    /**
     * Activate this document version as the single source of truth.
     */
    public function activate(): bool
    {
        $this->status = 'active';
        return $this->save();
    }
}
