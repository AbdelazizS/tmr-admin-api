<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'type' => $this->type,
            'price' => $this->price,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'area' => $this->area,
            'district' => $this->district,
            'location' => $this->location,
            'address' => $this->address,
            'year_built' => $this->year_built,
            'is_featured' => $this->is_featured,
            'video_tour_url' => $this->video_tour ? asset('storage/' . $this->video_tour) : null,
            'neighborhood' => $this->neighborhood,
            'features' => $this->features,
            'amenities' => $this->amenities,
            'images' => $this->images->map(fn($img) => asset('storage/' . $img->path)),
        ];
    }
}
