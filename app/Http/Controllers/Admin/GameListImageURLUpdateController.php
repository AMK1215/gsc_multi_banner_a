<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\GameList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameListImageURLUpdateController extends Controller
{
    /**
     * Display the list of games with images.
     */
    public function index()
    {
        $games = GameList::all(); // Retrieve all game records

        return view('admin.game_list_images', compact('games'));
    }

    /**
     * Update the image_url for a specific game.
     */
    public function edit(GameList $gameList)
    {
        return view('admin.game_list.edit', compact('gameList'));
    }

    public function updateImageUrl(Request $request, $id)
    {
        $game = GameList::findOrFail($id);

        // Validate the request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('image');

        if ($image && $image->isValid()) {
            // Use the original file name to store the image
            $filename = $image->getClientOriginalName();

            // Move the uploaded file to the public assets directory
            $image->move(public_path('assets/img/game_list/'), $filename);

            // Update the image_url column with the new file path
            $game->update([
                'image_url' => 'https://agdashboard.pro/assets/img/game_list/'.$filename,
            ]);

            return redirect()->route('admin.gameLists.index')->with('success', 'Image updated successfully.');
        }

        return redirect()->back()->withErrors('File upload failed.');
    }

    // public function updateImageUrl(Request $request, $id)
    // {
    //     $game = GameList::findOrFail($id);

    //     // Validate the request
    //     $request->validate([
    //         'image_url' => 'required|url',
    //     ]);

    //     // Update the image_url
    //     $game->update([
    //         'image_url' => $request->input('image_url'),
    //     ]);

    //     return redirect()->route('admin.gameLists.index')->with('success', 'Image URL updated successfully.');
    // }
    //     public function updateImageUrl(Request $request, $id)
    // {
    //     //dd($request->all());
    //     $game = GameList::findOrFail($id);

    //     // Validate the request
    //     $request->validate([
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate as an image file
    //     ]);

    //     $image = $request->file('image');

    //     // Use the original file name to store the image
    //     $filename = $image->getClientOriginalName();

    //     // Move the uploaded file to the public assets directory
    //     $image->move(public_path('assets/img/game_list/'), $filename);

    //     // Update the image_url column with the new file path
    //     $game->update([
    //         'image_url' => 'assets/img/game_list/' . $filename, // Store the relative path in the database
    //     ]);

    //     return redirect()->route('admin.gameLists.index')->with('success', 'Image updated successfully.');
    // }

    public function update(Request $request, $gameTypeId, $productId)
    {
        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension();
        $filename = uniqid('game_type').'.'.$ext;
        $image->move(public_path('assets/img/game_logo/'), $filename);

        DB::table('game_type_product')->where('game_type_id', $gameTypeId)->where('product_id', $productId)
            ->update(['image' => $filename]);

        return redirect()->route('admin.gametypes.index');
    }
}
