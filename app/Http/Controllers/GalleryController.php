<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;

class GalleryController extends Controller
{
    /**
     * Get gallery images for the authenticated user
     */
    public function myImages(Request $request)
    {
        $user = $request->user(); // Authenticated user

        $images = Gallery::where('user_id', $user->id)
            ->get()
            ->map(function($img) {
                $img->image_url = asset($img->image_path); // full URL
                return $img;
            });

        if ($images->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have no gallery images.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'user_id' => $user->id,
            'images' => $images
        ]);
    }
}
