<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    public function index()
    {
        return PropertyResource::collection(
            Property::with(['images', 'features', 'amenities', 'neighborhood.landmarks'])->latest()->paginate(10)
        );
    }

    public function store(StorePropertyRequest $request)
    {
        DB::transaction(function () use ($request) {
            $property = Property::create($request->validated());
            return $property ;

            // Relationships
            // inst $property->amenities()->sync($request->amenity_ids ?? []);
            $property->features()->createMany($request->features ?? []);

            // Media - images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $i => $img) {
                    $path = $img->store('properties', 'public');
                    $property->images()->create(['path' => $path, 'order' => $i]);
                }
            }

            // Media - video
            if ($request->hasFile('video_tour')) {
                $video = $request->file('video_tour')->store('videos', 'public');
                $property->update(['video_tour' => $video]);
            }

            // Neighborhood
            if ($request->filled('neighborhood.description')) {
                $neighborhood = $property->neighborhood()->create([
                    'description' => $request->input('neighborhood.description'),
                ]);

                if ($request->filled('neighborhood.landmarks')) {
                    foreach ($request->input('neighborhood.landmarks') as $name) {
                        $neighborhood->landmarks()->create(['name' => $name]);
                    }
                }
            }
        });

        return response()->json(['message' => 'Property created'], 201);
    }

    public function show(Property $property)
    {
        return new PropertyResource(
            $property->load(['images', 'features', 'amenities', 'neighborhood.landmarks'])
        );
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        DB::transaction(function () use ($request, $property) {
            $property->update($request->validated());

            // Relationships
            $property->amenities()->sync($request->amenity_ids ?? []);
            $property->features()->delete();
            $property->features()->createMany($request->features ?? []);

            // Neighborhood update
            if ($request->filled('neighborhood.description')) {
                $neighborhood = $property->neighborhood;

                if ($neighborhood) {
                    $neighborhood->update([
                        'description' => $request->input('neighborhood.description'),
                    ]);
                } else {
                    $neighborhood = $property->neighborhood()->create([
                        'description' => $request->input('neighborhood.description'),
                    ]);
                }

                // Replace landmarks
                if ($request->filled('neighborhood.landmarks')) {
                    $neighborhood->landmarks()->delete();
                    foreach ($request->input('neighborhood.landmarks') as $name) {
                        $neighborhood->landmarks()->create(['name' => $name]);
                    }
                }
            }
        });

        return response()->json(['message' => 'Property updated']);
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return response()->json(['message' => 'Property deleted']);
    }
}
