<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Item::orderBy('created_at', 'DESC')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $newItem = new Item;
        $newItem->name = $request->item["name"];
        $newItem->save();

        return $newItem;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $existingItem = Item::find($id);

        if ($existingItem) {
            $existingItem->completed = $request->item['completed'] ? true : false;
            $existingItem->completed_at = $request->item['completed'] ? Carbon::now() : null;
            $existingItem->save();
            return $existingItem;
        }
        return response()->json(['error' => 'Item not found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $existingItem = Item::find($id);

        if ($existingItem) {
            $existingItem->delete();
            return response()->json(['message' => 'Item successfully deleted']);
        }

        return response()->json(['error' => 'Item not found'], 404);
    }

    /**
     * Handle photo upload and approve completion.
     */
    public function approveCompletion(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $item = Item::find($id);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        // Generate path unik untuk file
        $filePath = 'uploads/items/' . $id . '/' . uniqid() . '.' . $request->file('photo')->getClientOriginalExtension();

        // Upload ke S3
        Storage::disk('s3')->put($filePath, file_get_contents($request->file('photo')), 'public');

        // Dapatkan URL dari S3
        $fileUrl = Storage::disk('s3')->url($filePath);

        // Simpan URL ke database
        $item->photo_url = $fileUrl;
        $item->completed = true;
        $item->completed_at = now();
        $item->save();

        return response()->json($item);
    }
}
