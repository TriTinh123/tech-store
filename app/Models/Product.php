<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'category_id',
        'price',
        'original_price',
        'stock',
        'image',
        'manufacturer',
        'specifications',
        'is_featured',
        'rating',
        'reviews_count',
    ];

    protected $casts = [
        'specifications' => 'array',
        'is_featured' => 'boolean',
        'price' => 'float',
        'original_price' => 'float',
    ];

    /**
     * Get the category of this product
     */
    public function categoryModel()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get reviews for this product
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get images for this product
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    /**
     * Get wishlist entries for this product
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price && $this->price < $this->original_price) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }

        return 0;
    }
}
