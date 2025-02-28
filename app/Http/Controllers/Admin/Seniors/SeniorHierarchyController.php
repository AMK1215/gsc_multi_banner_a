<?php

namespace App\Http\Controllers\Admin\Seniors;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class SeniorHierarchyController extends Controller
{
    /**
     * Display the hierarchical data for a senior.
     */
    // public function GetSeniorHierarchy()
    // {
    //     $senior_id = Auth::id(); // Authenticated user ID

    //     // Fetch the authenticated user
    //     $senior = User::with([
    //         'children' => function ($query) {
    //             // Fetch owners with their child agents
    //             $query->with([
    //                 'children' => function ($query) {
    //                     // Fetch agents with their child players
    //                     $query->with('children');
    //                 },
    //             ]);
    //         },
    //     ])->find($senior_id);

    //     // Check if the user exists and has the 'Senior' role
    //     if (! $senior || ! $senior->hasRole('Senior')) {
    //         return redirect()->back()->with('error', 'You are not authorized to view this hierarchy information.');
    //     }

    //     return view('admin.senior_info.index', ['senior' => $senior]);
    // }
    // public function GetSeniorHierarchy()
    // {
    //     $senior_id = Auth::id(); // Authenticated user ID

    //     // Fetch the authenticated user with hierarchical children
    //     $senior = User::with([
    //         'children' => function ($query) {
    //             $query->with([
    //                 'children' => function ($query) {
    //                     $query->with('children');
    //                 },
    //             ]);
    //         },
    //     ])->find($senior_id);

    //     // Check if the user exists and has the 'Senior' role
    //     if (!$senior || !$senior->hasRole('Senior')) {
    //         return redirect()->back()->with('error', 'You are not authorized to view this hierarchy information.');
    //     }

    //     // Group the data by Owner's Name and Agent's Name
    //     $groupedData = $senior->children->map(function ($owner) {
    //         return [
    //             'owner_name' => $owner->name,
    //             'owner_balance' => $owner->wallet->balanceFloat ?? '0.00',
    //             'agents' => $owner->children->groupBy('name')->map(function ($agents) {
    //                 return $agents->map(function ($agent) {
    //                     return [
    //                         'agent_name' => $agent->name,
    //                         'agent_balance' => $agent->wallet->balanceFloat ?? '0.00',
    //                         'players' => $agent->children->map(function ($player) {
    //                             return [
    //                                 'player_name' => $player->name,
    //                                 'player_balance' => $player->wallet->balanceFloat ?? '0.00',
    //                             ];
    //                         }),
    //                     ];
    //                 });
    //             }),
    //         ];
    //     });

    //     return view('admin.senior_info.index', [
    //         'groupedData' => $groupedData,
    //     ]);
    // }

    //     public function GetSeniorHierarchy(Request $request)
    // {
    //     $senior_id = Auth::id(); // Authenticated user ID

    //     // Fetch the authenticated user with hierarchical children
    //     $senior = User::with([
    //         'children' => function ($query) {
    //             $query->with([
    //                 'children' => function ($query) {
    //                     $query->with('children');
    //                 },
    //             ]);
    //         },
    //     ])->find($senior_id);

    //     // Check if the user exists and has the 'Senior' role
    //     if (!$senior || !$senior->hasRole('Senior')) {
    //         return redirect()->back()->with('error', 'You are not authorized to view this hierarchy information.');
    //     }

    //     // Group the data by Owner's Name and Agent's Name
    //     $groupedData = $senior->children->map(function ($owner) {
    //         return [
    //             'owner_name' => $owner->name,
    //             'owner_balance' => $owner->wallet->balanceFloat ?? '0.00',
    //             'agents' => $owner->children->groupBy('name')->map(function ($agents) {
    //                 return $agents->map(function ($agent) {
    //                     return [
    //                         'agent_name' => $agent->name,
    //                         'agent_balance' => $agent->wallet->balanceFloat ?? '0.00',
    //                         'players' => $agent->children->map(function ($player) {
    //                             return [
    //                                 'player_name' => $player->name,
    //                                 'player_balance' => $player->wallet->balanceFloat ?? '0.00',
    //                             ];
    //                         }),
    //                     ];
    //                 });
    //             }),
    //         ];
    //     });

    //     // Flatten the data for pagination
    //     $flattenedData = collect();
    //     foreach ($groupedData as $owner) {
    //         foreach ($owner['agents'] as $agentsGroup) {
    //             foreach ($agentsGroup as $agent) {
    //                 foreach ($agent['players'] as $player) {
    //                     $flattenedData->push([
    //                         'owner_name' => $owner['owner_name'],
    //                         'owner_balance' => $owner['owner_balance'],
    //                         'agent_name' => $agent['agent_name'],
    //                         'agent_balance' => $agent['agent_balance'],
    //                         'player_name' => $player['player_name'],
    //                         'player_balance' => $player['player_balance'],
    //                     ]);
    //                 }
    //             }
    //         }
    //     }

    //     // Manual Pagination
    //     $currentPage = LengthAwarePaginator::resolveCurrentPage();
    //     $perPage = 10; // Number of records per page
    //     $currentItems = $flattenedData->slice(($currentPage - 1) * $perPage, $perPage)->all();

    //     $paginatedData = new LengthAwarePaginator(
    //         $currentItems,
    //         $flattenedData->count(),
    //         $perPage,
    //         $currentPage,
    //         ['path' => $request->url(), 'query' => $request->query()]
    //     );

    //     return view('admin.senior_info.index', [
    //         'groupedData' => $paginatedData,
    //     ]);
    // }

    public function GetSeniorHierarchy(Request $request)
    {
        $senior_id = Auth::id(); // Authenticated user ID

        // Fetch the authenticated user with hierarchical children
        $senior = User::with([
            'children' => function ($query) {
                $query->with([
                    'children' => function ($query) {
                        $query->with('children');
                    },
                ]);
            },
        ])->find($senior_id);

        // Check if the user exists and has the 'Senior' role
        if (! $senior || ! $senior->hasRole('Senior')) {
            return redirect()->back()->with('error', 'You are not authorized to view this hierarchy information.');
        }

        // Variables to store total balances
        $totalOwnerBalance = 0;
        $totalAgentBalance = 0;
        $totalPlayerBalance = 0;

        // Group the data by Owner's Name and Agent's Name
        $groupedData = $senior->children->map(function ($owner) use (&$totalOwnerBalance, &$totalAgentBalance, &$totalPlayerBalance) {
            // Add Owner balance
            $ownerBalance = $owner->wallet->balanceFloat ?? 0.00;
            $totalOwnerBalance += $ownerBalance;

            return [
                'owner_name' => $owner->name,
                'owner_balance' => $ownerBalance,
                'agents' => $owner->children->groupBy('name')->map(function ($agents) use (&$totalAgentBalance, &$totalPlayerBalance) {
                    return $agents->map(function ($agent) use (&$totalAgentBalance, &$totalPlayerBalance) {
                        // Add Agent balance
                        $agentBalance = $agent->wallet->balanceFloat ?? 0.00;
                        $totalAgentBalance += $agentBalance;

                        return [
                            'agent_name' => $agent->name,
                            'agent_balance' => $agentBalance,
                            'players' => $agent->children->map(function ($player) use (&$totalPlayerBalance) {
                                // Add Player balance
                                $playerBalance = $player->wallet->balanceFloat ?? 0.00;
                                $totalPlayerBalance += $playerBalance;

                                return [
                                    'player_name' => $player->name,
                                    'player_balance' => $playerBalance,
                                ];
                            }),
                        ];
                    });
                }),
            ];
        });

        // Flatten the data for pagination
        $flattenedData = collect();
        foreach ($groupedData as $owner) {
            foreach ($owner['agents'] as $agentsGroup) {
                foreach ($agentsGroup as $agent) {
                    foreach ($agent['players'] as $player) {
                        $flattenedData->push([
                            'owner_name' => $owner['owner_name'],
                            'owner_balance' => $owner['owner_balance'],
                            'agent_name' => $agent['agent_name'],
                            'agent_balance' => $agent['agent_balance'],
                            'player_name' => $player['player_name'],
                            'player_balance' => $player['player_balance'],
                        ]);
                    }
                }
            }
        }

        // Manual Pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Number of records per page
        $currentItems = $flattenedData->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedData = new LengthAwarePaginator(
            $currentItems,
            $flattenedData->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Pass total balances to the view
        return view('admin.senior_info.index', [
            'groupedData' => $paginatedData,
            'totalOwnerBalance' => $totalOwnerBalance,
            'totalAgentBalance' => $totalAgentBalance,
            'totalPlayerBalance' => $totalPlayerBalance,
        ]);
    }

    public function getAllOwners()
    {
        // Retrieve all owners with their agents and respective wallets
        $owners = User::with(['agents.wallet']) // Load agents and their wallets
            ->whereHas('roles', function ($query) {
                $query->where('title', 'Owner'); // Filter only users with the Owner role
            })
            ->paginate(10);

        // Prepare the response data
        $owners->getCollection()->transform(function ($owner) {
            return [
                'id' => $owner->id, // Include owner ID for linking
                'owner_name' => $owner->name,
                'owner_id' => $owner->user_name,
                'owner_phone' => $owner->phone,
                'total_balance' => $owner->agents->reduce(function ($carry, $agent) {
                    return $carry + ($agent->wallet->balance ?? 0); // Sum up agents' wallet balances
                }, 0),
            ];
        });

        // Return the response to the view
        return view('admin.senior_info.owner_index', compact('owners'));
    }
    // v1
    public function getOwnerWithAgents($ownerId)
    {
        // Retrieve a specific owner with their agents and wallets
        $owner = User::with(['agents.wallet'])
            ->where('id', $ownerId) // Filter by owner ID
            ->firstOrFail();

        // Count the total agents and calculate total balance
        $totalAgents = $owner->agents->count();
        $totalBalance = $owner->agents->reduce(function ($carry, $agent) {
            return $carry + ($agent->wallet->balance ?? 0);
        }, 0);

        // Return the detail view with the owner information
        return view('admin.senior_info.owner_detail', compact('owner', 'totalAgents', 'totalBalance'));
    }

    public function getAgentDetails($owner_id, $agent_id)
{
    $agent = User::with('wallet')
        ->where('id', $agent_id)
        ->where('agent_id', $owner_id) // Ensuring agent belongs to the owner
        ->whereHas('roles', function ($query) {
            $query->where('title', 'Agent');
        })
        ->firstOrFail();

    $agentData = [
        'id' => $agent->id,
        'owner_id' => $owner_id,
        'agent_name' => $agent->name,
        'agent_username' => $agent->user_name,
        'agent_phone' => $agent->phone,
        'wallet_balance' => $agent->wallet->balanceFloat ?? 0,
    ];

    return view('admin.agent_info.details', compact('agentData'));
}


//     public function getAgentDetails($owner_id, $agent_id)
// {
//     // Retrieve the agent with wallet details
//     $agent = User::with('wallet')
//         ->where('id', $agent_id)
//         ->whereHas('roles', function ($query) {
//             $query->where('title', 'Agent'); // Ensure the user is an agent
//         })
//         ->firstOrFail(); // Fetch the agent or fail if not found

//     // Prepare the response data
//     $agentData = [
//         'id' => $agent->id,
//         'agent_name' => $agent->name,
//         'agent_username' => $agent->user_name,
//         'agent_phone' => $agent->phone,
//         'wallet_balance' => $agent->wallet->balance ?? 0, // Wallet balance (if exists)
//     ];

//     // Return view with agent details
//     return view('admin.agent_info.details', compact('agentData'));
// }


    // v2

//     public function getOwnerWithAgents($ownerId, Request $request)
// {
//     // Retrieve a specific owner with their agents and wallets
//     $owner = User::with(['agents.wallet'])
//         ->where('id', $ownerId)
//         ->firstOrFail();

//     // Count the total agents and calculate total balance for all agents
//     $totalAgents = $owner->agents->count();
//     $totalBalance = $owner->agents->reduce(function ($carry, $agent) {
//         return $carry + ($agent->wallet->balance ?? 0);
//     }, 0);

//     // Fetch the specific agent's balance if agentId is provided in the query string
//     $agentId = $request->query('agentId'); // Get agentId from query string
//     $specificAgentBalance = null;
//     $specificAgent = null;
//     if ($agentId) {
//         $specificAgent = $owner->agents->firstWhere('id', $agentId);
//         $specificAgentBalance = $specificAgent ? ($specificAgent->wallet->balance ?? 0) : null;
//     }

//     // Return the detail view with the owner information
//     return view('admin.senior_info.owner_detail', compact(
//         'owner',
//         'totalAgents',
//         'totalBalance',
//         'specificAgentBalance',
//         'specificAgent',
//         'agentId'
//     ));
// }
//     public function getOwnerWithAgents($ownerId, $agentId = null)
// {
//     // Retrieve a specific owner with their agents and wallets
//     $owner = User::with(['agents.wallet'])
//         ->where('id', $ownerId)
//         ->firstOrFail();

//     // Count the total agents and calculate total balance for all agents
//     $totalAgents = $owner->agents->count();
//     $totalBalance = $owner->agents->reduce(function ($carry, $agent) {
//         return $carry + ($agent->wallet->balance ?? 0);
//     }, 0);

//     // If a specific agent ID is provided, fetch that agent's balance
//     $specificAgentBalance = null;
//     $specificAgent = null;
//     if ($agentId) {
//         $specificAgent = $owner->agents->firstWhere('id', $agentId);
//         $specificAgentBalance = $specificAgent ? ($specificAgent->wallet->balance ?? 0) : null;
//     }

//     // Return the detail view with the owner information
//     return view('admin.senior_info.owner_detail', compact(
//         'owner',
//         'totalAgents',
//         'totalBalance',
//         'specificAgentBalance',
//         'specificAgent',
//         'agentId'
//     ));
// }

    public function getAgentWithPlayers($agentId)
    {
        // Retrieve the agent with their related players
        $agent = User::with('players.wallet') // Load players and their wallets
            ->where('id', $agentId) // Filter by agent ID
            ->firstOrFail();

        // Return the view with agent and related players
        return view('admin.senior_info.agent_detail', compact('agent'));
    }

    //     public function getAllOwners()
    // {
    //     // Retrieve all owners with their agents and respective wallets
    //     $owners = User::with(['agents.wallet']) // Load agents and their wallets
    //         ->whereHas('roles', function ($query) {
    //             $query->where('title', 'Owner'); // Filter only users with the Owner role
    //         })
    //         ->get();

    //     // Prepare the response data
    //     $data = $owners->map(function ($owner) {
    //         return [
    //             'owner_name' => $owner->name,
    //             'total_balance' => $owner->agents->reduce(function ($carry, $agent) {
    //                 return $carry + ($agent->wallet->balance ?? 0); // Sum up agents' wallet balances
    //             }, 0),
    //         ];
    //     });

    //     // Return the response as JSON
    //     //return response()->json($data);
    //     return view('admin.senior_info.owner_index', compact('data'));
    // }

    // public function GetSeniorHierarchy(Request $request)
    // {
    //     $senior_id = Auth::id(); // Authenticated user ID

    //     // Fetch the authenticated user with hierarchical children
    //     $senior = User::with([
    //         'children' => function ($query) {
    //             $query->with([
    //                 'children' => function ($query) {
    //                     $query->with('children');
    //                 },
    //             ]);
    //         },
    //     ])->find($senior_id);

    //     // Check if the user exists and has the 'Senior' role
    //     if (!$senior || !$senior->hasRole('Senior')) {
    //         return redirect()->back()->with('error', 'You are not authorized to view this hierarchy information.');
    //     }

    //     // Variables to store total balances
    //     $totalOwnerBalance = 0;
    //     $totalAgentBalance = 0;
    //     $totalPlayerBalance = 0;

    //     // Process the data hierarchy
    //     $groupedData = $senior->children->map(function ($owner) use (&$totalOwnerBalance, &$totalAgentBalance, &$totalPlayerBalance) {
    //         // Owner balance
    //         $ownerBalance = $owner->wallet->balanceFloat ?? 0.00;
    //         $totalOwnerBalance += $ownerBalance;

    //         // Total agent balance under this owner
    //         $ownerAgentTotalBalance = 0;

    //         $agentsData = $owner->children->map(function ($agent) use (&$totalAgentBalance, &$totalPlayerBalance, &$ownerAgentTotalBalance) {
    //             // Agent balance
    //             $agentBalance = $agent->wallet->balanceFloat ?? 0.00;
    //             $totalAgentBalance += $agentBalance;
    //             $ownerAgentTotalBalance += $agentBalance; // Add to owner's agent total

    //             // Players under the agent
    //             $playersData = $agent->children->map(function ($player) use (&$totalPlayerBalance) {
    //                 $playerBalance = $player->wallet->balanceFloat ?? 0.00;
    //                 $totalPlayerBalance += $playerBalance;

    //                 return [
    //                     'player_name' => $player->name,
    //                     'player_balance' => number_format($playerBalance, 2),
    //                 ];
    //             });

    //             return [
    //                 'agent_name' => $agent->name,
    //                 'agent_balance' => number_format($agentBalance, 2),
    //                 'players' => $playersData,
    //             ];
    //         });

    //         return [
    //             'owner_name' => $owner->name,
    //             'owner_balance' => number_format($ownerBalance, 2),
    //             'owner_agent_total_balance' => number_format($ownerAgentTotalBalance, 2), // Total of all agents under this owner
    //             'agents' => $agentsData,
    //         ];
    //     });

    //     // Flatten the data for pagination
    //     $flattenedData = collect();
    //     foreach ($groupedData as $owner) {
    //         foreach ($owner['agents'] as $agent) {
    //             foreach ($agent['players'] as $player) {
    //                 $flattenedData->push([
    //                     'owner_name' => $owner['owner_name'],
    //                     'owner_balance' => $owner['owner_balance'],
    //                     'owner_agent_total_balance' => $owner['owner_agent_total_balance'],
    //                     'agent_name' => $agent['agent_name'],
    //                     'agent_balance' => $agent['agent_balance'],
    //                     'player_name' => $player['player_name'],
    //                     'player_balance' => $player['player_balance'],
    //                 ]);
    //             }
    //         }
    //     }

    //     // Manual Pagination
    //     $currentPage = LengthAwarePaginator::resolveCurrentPage();
    //     $perPage = 10; // Records per page
    //     $currentItems = $flattenedData->slice(($currentPage - 1) * $perPage, $perPage)->all();

    //     $paginatedData = new LengthAwarePaginator(
    //         $currentItems,
    //         $flattenedData->count(),
    //         $perPage,
    //         $currentPage,
    //         ['path' => $request->url(), 'query' => $request->query()]
    //     );

    //     // Pass data to view
    //     return view('admin.senior_info.index', [
    //         'groupedData' => $paginatedData,
    //         'totalOwnerBalance' => number_format($totalOwnerBalance, 2),
    //         'totalAgentBalance' => number_format($totalAgentBalance, 2),
    //         'totalPlayerBalance' => number_format($totalPlayerBalance, 2),
    //     ]);
    // }

}