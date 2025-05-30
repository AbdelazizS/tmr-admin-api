<?php
namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Amenity;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['images', 'features', 'amenities', 'neighborhood.landmarks']);

        // Filter by bathrooms No
        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', $request->bathrooms);
        }

        // Filter by bedrooms No
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', $request->bedrooms);
        }
        
        // Filter by type (rent, for-sale, etc.)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Fulltext-like search (title, location, description)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        return PropertyResource::collection($query->latest()->get());
    }

    public function store(StorePropertyRequest $request)
    {

        return DB::transaction(function () use ($request) {
            try {
                // 1. Create Property
                $property = Property::create($request->safe()->except([
                    'amenities',
                    'features',
                    'images',
                    'video_tour',
                    'neighborhood',
                ]));

                // 2. Handle Relationships
                $this->handlePropertyRelationships($property, $request);

                // 3. Handle Media Uploads
                $this->handleMediaUploads($property, $request);

                // 4. Return Response
                return new PropertyResource(
                    $property->load(['amenities', 'features', 'images', 'neighborhood.landmarks'])
                );

            } catch (\Exception $e) {
                            // 5. Transaction will auto-rollback on exception
                report($e); // Log the error
                throw $e;   // Re-throw for controller exception handler
            }
        });
    }

    protected function handlePropertyRelationships(Property $property, $request)
    {
        // Handle Amenities
        if ($request->filled('amenities')) {
            $amenities = array_map(function ($amenity) {
                return new Amenity([
                    'name'     => $amenity['name'],
                    'distance' => $amenity['distance'],
                ]);
            }, $request->amenities);

            $property->amenities()->saveMany($amenities);
        }

        // Handle Features
        if ($request->filled('features')) {
            $property->features()->createMany(
                collect($request->features)->map(fn($f) => ['feature' => $f['feature']])
            );
        }

        // Handle Neighborhood

        if ($request->filled('neighborhood.description')) {
            $neighborhood = $property->neighborhood()->create([
                'description' => $request->input('neighborhood.description'),
            ]);

            if ($request->filled('neighborhood.landmarks')) {
                $neighborhood->landmarks()->createMany(
                    collect($request->input('neighborhood.landmarks'))
                        ->map(fn($l) => ['name' => $l['name'], 'distance' => $l['distance']])
                );
            }
        }
    }

    protected function handleMediaUploads(Property $property, $request)
    {
        // Handle Images
        if ($request->hasFile('images')) {
            $images = collect($request->file('images'))->map(function ($file, $index) {
                return [
                    'path'  => $file->store('properties', 'public'),
                    'order' => $index,
                ];
            });

            $property->images()->createMany($images);
        }

        // Handle Video
        if ($request->hasFile('video_tour')) {
            $property->update([
                'video_tour' => $request->file('video_tour')->store('videos', 'public'),
            ]);
        }
    }
    public function show($identifier)
    {
        $property = Property::with(['images', 'features', 'amenities', 'neighborhood.landmarks'])
            ->where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->firstOrFail();

        return new PropertyResource($property);
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
