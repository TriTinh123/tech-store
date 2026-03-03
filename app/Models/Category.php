<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
    ];

    /**
     * Get products in this category
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
