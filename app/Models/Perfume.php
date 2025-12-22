<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfume extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'stock'];

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
}
