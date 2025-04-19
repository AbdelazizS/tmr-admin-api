<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                    => 'required|string|max:255',
            'description'              => 'required|string',
            'status'                   => 'required',
            'type'                     => 'required',
            'price'                    => 'required|numeric|min:0',
            'bedrooms'                 => 'required|integer|min:0',
            'bathrooms'                => 'required|integer|min:0',
            'area'                     => 'required|numeric|min:0',
            'district'                 => 'required|string',
            'location'                 => 'required|string',
            'address'                  => 'required|string',
            'year_built'               => 'required|digits:4',
            'is_featured'              => 'boolean',
            'video_tour'               => 'nullable|file|mimes:mp4|max:20000',
            'neighborhood'             => 'nullable|array',
            'neighborhood.description' => 'nullable|string',
            'neighborhood.landmarks'   => 'nullable|array',
            'amenities'                => 'sometimes|array',
            'amenities.*.name'         => 'required|string|max:255',
            'features'                 => 'sometimes|array',
            'features.*.feature'       => 'required|string|max:255',
            'images.*'                 => 'nullable|image|max:2048',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('amenities') && is_string($this->amenities)) {
            $this->merge([
                'amenities' => json_decode($this->amenities, true),
            ]);
        }

        if ($this->has('features') && is_string($this->features)) {
            $this->merge([
                'features' => json_decode($this->features, true),
            ]);
        }

            if ($this->has('neighborhood') && is_string($this->neighborhood)) {
                $this->merge([
                    'neighborhood' => json_decode($this->neighborhood, true),
                ]);
            }
    }
}
