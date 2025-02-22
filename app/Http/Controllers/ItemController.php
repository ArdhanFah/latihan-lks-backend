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
        $exitstingItem = Item::find($id);

        if($exitstingItem){
            $exitstingItem->completed = $request->item['completed'] ? true : false;
            $exitstingItem->completed_at = $request->item['completed'] ? Carbon::now() : null;
            $exitstingItem->save();
            return $exitstingItem;
        }
        return "Item Not Found";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $exitstingItem = Item::find($id);

        if($exitstingItem){
            $exitstingItem->delete();
            return "Item Succesfully Delete";
        }

        return "Item Not Found";
    }
}
