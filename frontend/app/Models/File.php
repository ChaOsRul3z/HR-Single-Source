<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class File extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'original_name',
        'storage_path',
        'mime_type',
        'size',
        'file_hash',
    ];

    /**
     * Get a human-readable file size (e.g., 2.4 MB).
     * Usage: $file->readable_size
     */
    protected function readableSize(): Attribute
    {
        return Attribute::get(function () {
            $bytes = $this->size;
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];

            for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                $bytes /= 1024;
            }

            return round($bytes, 2) . ' ' . $units[$i];
        });
    }

    /**
     * Get the full, public or private URL to access the file.
     * Usage: $file->file_url
     */
    protected function fileUrl(): Attribute
    {
        return Attribute::get(fn () => Storage::url($this->storage_path));
    }
}
