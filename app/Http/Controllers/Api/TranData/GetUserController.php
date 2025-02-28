<?php

namespace App\Http\Controllers\Api\TranData;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class GetUserController extends Controller
{
    use HttpResponses;

    /**
     * Get all users.
     */
    public function getAllUsers()
    {
        try {
            // Fetch all users with their relationships (if needed)
            $users = User::all();

            // Return successful response
            return $this->success($users, 'Users fetched successfully.');
        } catch (\Exception $e) {
            // Return error response
            return $this->error(null, 'Failed to fetch users.', 500);
        }
    }
}
