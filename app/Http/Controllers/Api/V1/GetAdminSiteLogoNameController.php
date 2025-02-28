<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetAdminSiteLogoNameController extends Controller
{
    use HttpResponses;

    public function GetSiteLogoAndSiteName()
    {
        if (Auth::check()) {
            $user = Auth::user();

            $adminLogo = $user->agent_logo
                ? asset('assets/img/logo/'.$user->agent_logo)
                : asset('assets/img/logo/default-logo.jpg');

            $siteName = $user->site_name ?? 'GoldenJack';

            return $this->success(
                [
                    'adminLogo' => $adminLogo,
                    'siteName' => $siteName,
                ],
                'Admin details retrieved successfully.'
            );
        }

        return $this->error('Unauthorized', null, 401);
    }

    // public function getDetails()
    // {
    //     if (Auth::check()) {
    //         $user = Auth::user();

    //         $adminLogo = $user->agent_logo
    //             ? asset('assets/img/logo/' . $user->agent_logo)
    //             : asset('assets/img/logo/default-logo.jpg');

    //         $siteName = $user->site_name ?? 'GoldenJack';

    //         return response()->json([
    //             'status' => 200,
    //             'data' => [
    //                 'adminLogo' => $adminLogo,
    //                 'siteName' => $siteName,
    //             ],
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => 401,
    //         'message' => 'Unauthorized',
    //     ], 401);
    // }
}
