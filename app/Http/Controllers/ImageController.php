<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Perfume;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{

    public function index(Perfume $perfume)
    {
        return response()->json($perfume->images);
    }


    public function store(Request $request, Perfume $perfume)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_primary' => 'sometimes|boolean'
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $index => $imageFile) {
            $filename = time() . '_' . $index . '_' . $imageFile->getClientOriginalName();
            $path = $imageFile->storeAs('perfumes', $filename, 'public');

            
            $isPrimary = ($index === 0 && $request->get('is_primary', false)) 
                         || ($index === 0 && !$perfume->primaryImage);

            $image = $perfume->images()->create([
                'filename' => $filename,
                'path' => $path,
                'alt_text' => $request->get('alt_text', $perfume->name),
                'is_primary' => $isPrimary
            ]);

            $uploadedImages[] = $image;
        }

        return response()->json([
            'message' => 'Images uploaded successfully',
            'images' => $uploadedImages
        ], 201);
    }

    public function storeSingle(Request $request, Perfume $perfume)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'alt_text' => 'sometimes|string|max:255',
            'is_primary' => 'sometimes|boolean'
        ]);

        $imageFile = $request->file('image');
        $filename = time() . '_' . $imageFile->getClientOriginalName();
        $path = $imageFile->storeAs('perfumes', $filename, 'public');

        
        if ($request->get('is_primary', false)) {
            $perfume->images()->update(['is_primary' => false]);
        }

        $image = $perfume->images()->create([
            'filename' => $filename,
            'path' => $path,
            'alt_text' => $request->get('alt_text', $perfume->name),
            'is_primary' => $request->get('is_primary', false)
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image' => $image
        ], 201);
    }


    public function setPrimary(Perfume $perfume, Image $image)
    {
        
        if ($image->perfume_id !== $perfume->id) {
            return response()->json(['message' => 'Image not found for this perfume'], 404);
        }

        
        $perfume->images()->update(['is_primary' => false]);
        
        
        $image->update(['is_primary' => true]);

        return response()->json([
            'message' => 'Image set as primary',
            'image' => $image
        ]);
    }

    public function destroy(Perfume $perfume, Image $image)
    {
        
        if ($image->perfume_id !== $perfume->id) {
            return response()->json(['message' => 'Image not found for this perfume'], 404);
        }

        
        Storage::disk('public')->delete($image->path);
        
        
        $image->delete();

        return response()->json(null, 204);
    }
}
