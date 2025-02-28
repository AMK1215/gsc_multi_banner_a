<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\GameList;
use App\Models\Admin\GameType;
use App\Models\Admin\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewGameListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gameLists = GameList::with(['product', 'gameType'])->get();

        return view('admin.new_game_list.index', compact('gameLists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch related data (game types, products) for the form
        $gameTypes = GameType::all();
        $products = Product::all();

        return view('admin.new_game_list.create', compact('gameTypes', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // $validatedData = $request->validate([
        //     'game_id' => 'required|integer',
        //     'game_type_id' => 'required|exists:game_types,id',
        //     'product_id' => 'required|exists:products,id',
        //     'status' => 'required|boolean',
        //     'game_code' => 'required|string|max:50',
        //     'game_name' => 'required|string|max:100',
        //     //'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        //     'image_url' => 'nullable|string',
        //     'method' => 'required|string',
        //     'sequence' => 'required|integer',
        //     'game_provide_code' => 'required|string|max:50',
        //     'game_provide_name' => 'required|string|max:100',
        // ]);
        $validatedData = $request->validate([
            'game_id' => 'required|integer',
            'game_type_id' => 'required|exists:game_types,id',
            'product_id' => 'required|exists:products,id',
            'status' => 'required|boolean',
            'game_code' => 'required|string|max:50',
            'game_name' => 'required|string|max:100',
            'game_type' => 'required|integer', // Add this line
            'image_url' => 'nullable|string',
            'method' => 'required|string',
            'is_h5_support' => 'required',
            'has_demo' => 'required',
            'sequence' => 'required|integer',
            'game_provide_code' => 'required|string|max:50',
            'game_provide_name' => 'required|string|max:100',
            'order' => 'required|integer',
        ]);

        // Create the game list entry
        GameList::create($validatedData);

        return redirect()->route('admin.gamelistnew.index')->with('success', 'New Game List created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GameList $gameList)
    {
        $gameTypes = GameType::all();
        $products = Product::all();

        return view('admin.new_game_list.edit', compact('gameList', 'gameTypes', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GameList $gameList)
    {
        $validatedData = $request->validate([
            'game_id' => 'required|integer',
            'game_type_id' => 'required|exists:game_types,id',
            'product_id' => 'required|exists:products,id',
            'status' => 'required|boolean',
            'game_code' => 'required|string|max:50',
            'game_name' => 'required|string|max:100',
            //'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image' => 'nullable|string',
            'method' => 'required|string',
            'sequence' => 'required|integer',
            'game_provide_code' => 'required|string|max:50',
            'game_provide_name' => 'required|string|max:100',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete the old image
            if ($gameList->image_url) {
                Storage::delete('public/'.$gameList->image_url);
            }

            $path = $request->file('image')->store('game_logo', 'public');
            $validatedData['image_url'] = $path;
        }

        // Update the game list entry
        $gameList->update($validatedData);

        return redirect()->route('admin.gamelistnew.index')->with('success', 'Game updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameList $gameList)
    {
        // Delete the image file
        if ($gameList->image_url) {
            Storage::delete('public/'.$gameList->image_url);
        }

        $gameList->delete();

        return redirect()->route('admin.gamelistnew.index')->with('success', 'Game deleted successfully.');
    }
}
