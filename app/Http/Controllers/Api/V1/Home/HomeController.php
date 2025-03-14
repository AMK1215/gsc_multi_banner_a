<?php

namespace App\Http\Controllers\Api\V1\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AdsBannerResource;
use App\Http\Resources\Api\BannerResource;
use App\Http\Resources\Api\BannerTextResource;
use App\Http\Resources\Api\ContactResource;
use App\Http\Resources\Api\PromotionResource;
use App\Models\Admin\AdsVedio;
use App\Models\Admin\Banner;
use App\Models\Admin\BannerAds;
use App\Models\Admin\BannerText;
use App\Models\Admin\Promotion;
use App\Models\Admin\TopTenWithdraw;
use App\Models\Contact;
use App\Models\WinnerText;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    use HttpResponses;

    public function index()
    {
        $user = Auth::user();
        $agent = $user->parent->id;
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
        $contacts = Contact::where('agent_id', $agent)->get();

        return $this->success([
            "banners" => BannerResource::collection($banners),
            "banner_text" => new BannerTextResource($banner_text),
            "ads_banner" => new AdsBannerResource($ads_banner),
            "rewards" => $rewards,
            "promotions" => PromotionResource::collection($promotions),
            "contacts" => ContactResource::collection($contacts)
        ]);
    }
}
