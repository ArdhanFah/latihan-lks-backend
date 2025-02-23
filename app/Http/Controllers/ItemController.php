<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Carbon;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Item::orderBy('created_at', 'DESC')->get();
    }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     //
    // }

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
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
               // Cari item berdasarkan ID
        $item = Item::findOrFail($id);

        // Validasi request
        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'completed' => 'required|boolean',
        ]);

        // Simpan gambar jika ada file yang diunggah
        if ($request->hasFile('proof_image')) {
            $path = $request->file('proof_image')->store('proofs', 'public');
            $item->proof_image = $path;
        }

        // Perbarui status penyelesaian
        $item->completed = $request->completed;

        // Jika tugas diselesaikan, tambahkan timestamp completed_at
        $item->completed_at = $request->completed ? Carbon::now() : null;

        // Simpan perubahan
        $item->save();

        return response()->json($item, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item Not Found'], 404); // Return HTTP 404 Not Found
        }

        // Hapus gambar terkait jika ada
        if ($item->proof_image && Storage::disk('public')->exists($item->proof_image)) {
            Storage::disk('public')->delete($item->proof_image);
        }

        // Hapus item dari database
        $item->delete();

        return response()->json(['message' => 'Item Successfully Deleted'], 200); 
    }
}
