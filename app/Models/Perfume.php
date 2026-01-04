<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfume extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'brand', 'description', 'price', 'stock'];
    
    protected $appends = ['display_image'];

    /**
     * Get all images for the perfume.
     */
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Get the primary image for the perfume.
     */
    public function primaryImage()
    {
        return $this->hasOne(Image::class)->where('is_primary', true);
    }

    /**
     * Get the display image (primary or first available).
     */
    public function getDisplayImageAttribute()
    {
        $primary = $this->primaryImage;
        if ($primary) {
            return $primary;
        }
        
        return $this->images()->first();
    }
}
