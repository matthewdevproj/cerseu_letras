<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'type',
        'title',
        'original_name',
        'url',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    /**
     * Get title for display (uses title if set, otherwise original_name).
     */
    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?: $this->original_name;
    }

    /**
     * Scope for published documents.
     */
    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    /**
     * Scope by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the file extension.
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    /**
     * Check if document is a PDF.
     */
    public function getIsPdfAttribute(): bool
    {
        return strtolower($this->extension) === 'pdf';
    }

    /**
     * Check if document is an image.
     */
    public function getIsImageAttribute(): bool
    {
        return in_array(strtolower($this->extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }
}
