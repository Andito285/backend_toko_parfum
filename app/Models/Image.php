<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'perfume_id',
        'filename',
        'path',
        'alt_text',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected $appends = ['url'];

    /**
     * Get the full URL for the image.
     */
    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->path);
    }

    public function perfume()
    {
        return $this->belongsTo(Perfume::class);
    }
}
