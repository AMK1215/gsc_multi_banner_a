<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\GameList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class GameListController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = GameList::with(['gameType', 'product']);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('order', function ($row) {
                    return $row->order;
                })
                ->addColumn('game_type', function ($row) {
                    return $row->gameType->name ?? 'N/A';
                })
                ->addColumn('product', function ($row) {
                    return $row->product->provider_name ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return $row->status == 1 ? 'Running Game' : 'Game is Closed';
                })
                ->addColumn('pp_hot', function ($row) {
                    return $row->pp_hot == 1 ? 'PP Hot' : '--';
                })
                ->addColumn('hot_status', function ($row) {
                    return $row->hot_status == 1 ? 'This Game is Hot' : 'Game is Normal';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<form action="'.route('admin.gameLists.toggleStatus', $row->id).'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('PATCH').'
                                <button type="submit" class="btn btn-warning btn-sm">GameStatus</button>
                            </form>';
                    $btn .= '<form action="'.route('admin.HotGame.toggleStatus', $row->id).'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('PATCH').'
                                <button type="submit" class="btn btn-success btn-sm">HotGame</button>
                            </form>';

                    $btn .= '<form action="'.route('admin.PPHotGame.toggleStatus', $row->id).'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('PATCH').'
                                <button type="submit" class="btn btn-warning btn-sm">PPHot</button>
                            </form>';

                    $btn .= '<a href="'.route('admin.game_list.edit', $row->id).'" class="btn btn-primary btn-sm">EditImageURL</a>';
                    $btn .= '<a href="'.route('admin.game_list_order.edit', $row->id).'" class="btn btn-primary btn-sm">Order</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.game_list.paginate_index');
    }

    public function edit($gameTypeId, $productId)
    {
        $gameType = GameList::with([
            'products' => function ($query) use ($productId) {
                $query->where('products.id', $productId);
            },
        ])->where('id', $gameTypeId)->first();

        return view('admin.game_type.edit', compact('gameType', 'productId'));
    }

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

    public function toggleStatus($id)
    {
        $game = GameList::findOrFail($id);
        $game->status = $game->status == 1 ? 0 : 1;
        $game->save();

        return redirect()->route('admin.gameLists.index')->with('success', 'Game status updated successfully.');
    }

    public function HotGameStatus($id)
    {
        $game = GameList::findOrFail($id);
        $game->hot_status = $game->hot_status == 1 ? 0 : 1;
        $game->save();

        return redirect()->route('admin.gameLists.index')->with('success', 'HotGame status updated successfully.');
    }

    public function PPHotGameStatus($id)
    {
        $game = GameList::findOrFail($id);
        $game->pp_hot = $game->pp_hot == 1 ? 0 : 1;
        $game->save();

        return redirect()->route('admin.gameLists.index')->with('success', 'PP HotGame status updated successfully.');
    }

    public function GameListOrderedit(GameList $gameList)
    {
        return view('admin.game_list.order_edit', compact('gameList'));
    }

    public function updateOrder(Request $request, $id)
    {
        // Validate the form input
        $request->validate([
            'order' => 'required|integer|min:0',
        ]);

        // Find the game list record
        $gameList = GameList::findOrFail($id);

        // Update the order column
        $gameList->order = $request->input('order');
        $gameList->save();

        return redirect()->route('admin.gameLists.index')->with('success', 'Game list order  updated successfully.');

        // Return a response
        // return response()->json([
        //     'message' => 'Order updated successfully.',
        //     'data' => $gameList,
        // ]);
    }

    public function GetsearchGames(Request $request)
    {
        return view('admin.game_list.search_index');
    }

    public function searchGames(Request $request)
    {
        // Validate search input (optional)
        $request->validate([
            'game_name' => 'nullable|string',
        ]);

        // Build query
        $query = GameList::query();

        // Add filters based on request inputs
        if ($request->filled('game_name')) {
            $query->where('game_name', 'LIKE', '%'.$request->input('game_name').'%');
        }

        // Execute query and get results
        $games = $query->get();

        return view('admin.game_list.search', compact('games'));

    }

    public function updateAllOrder(Request $request)
    {
        // Validate the input
        $request->validate([
            'order' => 'required|integer',
        ]);

        // Get the new order value from the request
        $newOrderValue = $request->input('order');

        // Perform the bulk update
        $updatedCount = GameList::query()->update(['order' => $newOrderValue]);

        // Redirect back with a success message
        return redirect()
            ->back()
            ->with('success', "Order column updated for all rows successfully. Updated rows: $updatedCount.");
    }
    // public function updateAllOrder(Request $request)
    // {
    //     // Validate the input
    //     $request->validate([
    //         'order' => 'required|integer',
    //     ]);

    //     // Get the new order value from the request
    //     $newOrderValue = $request->input('order');

    //     // Perform the bulk update
    //     $updatedCount = GameList::query()->update(['order' => $newOrderValue]);

    //     // Return a response
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Order column updated for all rows successfully.',
    //         'updated_rows' => $updatedCount,
    //     ]);
    // }
}
