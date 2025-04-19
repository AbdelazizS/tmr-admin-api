<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    public function index()
    {
        return PropertyResource::collection(Property::latest()->paginate(10));
    }

    public function store(StorePropertyRequest $request)
    {
        DB::transaction(function () use ($request) {
            $property = Property::create($request->validated());
            $property->amenities()->sync($request->amenity_ids ?? []);
            $property->features()->createMany($request->features ?? []);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $i => $img) {
                    $path = $img->store('properties', 'public');
                    $property->images()->create(['path' => $path, 'order' => $i]);
                }
            }
            if ($request->hasFile('video_tour')) {
                $video = $request->file('video_tour')->store('videos', 'public');
                $property->update(['video_tour' => $video]);
            }
        });
        return response()->json(['message' => 'Property created'], 201);
    }

    public function show(Property $property)
    {
        return new PropertyResource($property->load(['images', 'features', 'amenities']));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        DB::transaction(function () use ($request, $property) {
            $property->update($request->validated());
            $property->amenities()->sync($request->amenity_ids ?? []);
            $property->features()->delete();
            $property->features()->createMany($request->features ?? []);
        });
        return response()->json(['message' => 'Property updated']);
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return response()->json(['message' => 'Property deleted']);
    }
}
