<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AdsBannerResource;
use App\Http\Resources\Api\BannerResource;
use App\Http\Resources\Api\BannerTextResource;
use App\Http\Resources\Api\PromotionResource;
use App\Models\Admin\AdsVedio;
use App\Models\Admin\Banner;
use App\Models\Admin\BannerAds;
use App\Models\Admin\BannerText;
use App\Models\Admin\Promotion;
use App\Models\Admin\TopTenWithdraw;
use App\Models\WinnerText;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    use HttpResponses;

    public function index()
    {
        $user = Auth::user();
        if ($user->parent) {
            $admin = $user->parent->parent->id;
        } else {
            $admin = $user->id;
        }
        $banners = Banner::where('admin_id', $admin)->get();
        $rewards = TopTenWithdraw::where('admin_id', $admin)->get();
        $banner_text = BannerText::where('admin_id', $admin)->latest()->first();
        $ads_banner = BannerAds::where('admin_id', $admin)->latest()->first();
        $promotions = Promotion::where('admin_id', $admin)->latest()->get();

        return $this->success([
            "banners" => BannerResource::collection($banners),
            "banner_text" => new BannerTextResource($banner_text),
            "ads_banner" => new AdsBannerResource($ads_banner),
            "rewards" => $rewards,
            "promotions" => PromotionResource::collection($promotions)
        ]);
    }
    public function banners()
    {
        $user = Auth::user();

        // Determine the admin whose banners to fetch
        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        // Fetch banners for the determined admin
        $data = Banner::where('admin_id', $admin->id)->get();

        return $this->success($data, 'Banners retrieved successfully.');
    }

    public function TopTen()
    {
        $user = Auth::user();

        // Determine the admin whose banners to fetch
        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        // Fetch banners for the determined admin
        $data = TopTenWithdraw::where('admin_id', $admin->id)->get();

        return $this->success($data, 'TopTen Winner retrieved successfully.');
    }

    public function ApiVideoads()
    {
        $user = Auth::user();

        // Determine the admin whose banners to fetch
        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        // Fetch banners for the determined admin
        $data = AdsVedio::where('admin_id', $admin->id)->get();

        return $this->success($data, 'AdsVedio retrieved successfully.');
    }

    public function bannerText()
    {
        $user = Auth::user();
        if ($user->parent) {
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            $admin = $user;
        }
        $data = BannerText::where('admin_id', $admin->id)->latest()->first();
        return $this->success($data, message: 'BannerTexts retrieved successfully.');
    }

    public function adsBanner()
    {
        $user = Auth::user();
        //dd($user);

        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        // Fetch banners for the determined admin
        $data = BannerAds::where('admin_id', $admin->id)->get();

        return $this->success($data, 'BannerAds retrieved successfully.');
    }

    public function AdsBannerTest()
    {
        $user = Auth::user();
        //dd($user);

        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        // Fetch banners for the determined admin
        $data = BannerAds::where('admin_id', $admin->id)->latest()->first();

        return $this->success($data, 'BannerAds retrieved successfully.');
    }

    public function bannerTest()
    {
        $user = Auth::user();

        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        $data = BannerText::where('admin_id', $admin->id)->latest()->first();

        return $this->success($data, 'BannerTexts retrieved successfully.');
    }

    public function winnerText()
    {
        $user = Auth::user();

        if ($user->parent) {
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            $admin = $user;
        }

        $data = WinnerText::where('owner_id', $admin->id)->latest()->first();

        return $this->success($data, 'Winner Text retrieved successfully.');

    }
}
