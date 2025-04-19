<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'title', 'description', 'status', 'type', 'price', 'bedrooms',
        'bathrooms', 'area', 'district', 'location', 'address', 'year_built',
        'video_tour', 'neighborhood', 'is_featured'
    ];

    protected $casts = [
        'neighborhood' => 'array',
        'is_featured' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($property) {
            $property->slug = Str::slug($property->title) . '-' . Str::random(5);
        });
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function features()
    {
        return $this->hasMany(Feature::class);
    }

    public function neighborhood() {
        return $this->hasOne(Neighborhood::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class)->orderBy('order');
    }
}
