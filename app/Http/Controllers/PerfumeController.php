<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perfume;

class PerfumeController extends Controller
{

public function index()
{
    $perfumes = Perfume::with('images')->get();
    
    return response()->json($perfumes->map(function ($perfume) {
        return $this->formatPerfumeResponse($perfume);
    }));
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'brand' => 'nullable|string|max:255',
        'description' => 'required',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0'
    ]);

    $perfume = Perfume::create($request->all());
    return response()->json($perfume, 201);
}

public function show(Perfume $perfume)
{
    $perfume->load('images');
    return response()->json($this->formatPerfumeResponse($perfume));
}

public function update(Request $request, Perfume $perfume)
{
    $request->validate([
        'name' => 'sometimes|string|max:255',
        'brand' => 'nullable|string|max:255',
        'description' => 'sometimes|string',
        'price' => 'sometimes|numeric|min:0',
        'stock' => 'sometimes|integer|min:0'    
    ]);

    $perfume->update($request->only(['name', 'brand', 'description', 'price', 'stock']));

    return response()->json($perfume, 200);
}

public function destroy(Perfume $perfume)
{
    $perfume->delete();
    return response()->json(null, 204);
}

private function formatPerfumeResponse(Perfume $perfume)
{
    $primaryImage = $perfume->images->firstWhere('is_primary', true) 
                    ?? $perfume->images->first();
    
    return [
        'id' => $perfume->id,
        'name' => $perfume->name,
        'brand' => $perfume->brand,
        'description' => $perfume->description,
        'price' => $perfume->price,
        'stock' => $perfume->stock,
        'created_at' => $perfume->created_at,
        'updated_at' => $perfume->updated_at,
        'primary_image' => $primaryImage ? [
            'id' => $primaryImage->id,
            'url' => asset('storage/' . $primaryImage->path),
            'alt_text' => $primaryImage->alt_text
        ] : null,
        'images' => $perfume->images->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => asset('storage/' . $image->path),
                'alt_text' => $image->alt_text,
                'is_primary' => $image->is_primary
            ];
        })
    ];
}
}

