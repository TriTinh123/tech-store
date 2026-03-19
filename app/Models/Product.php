<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Product Model
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string|null $category
 * @property int $category_id
 * @property float $price
 * @property float $original_price
 * @property int $stock
 * @property string|null $image
 * @property string|null $manufacturer
 * @property array|null $specifications
 * @property bool $is_featured
 * @property float $rating
 * @property int $reviews_count
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
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
     * Get the reviews for this product
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price && $this->price < $this->original_price) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return 0;
    }
}
