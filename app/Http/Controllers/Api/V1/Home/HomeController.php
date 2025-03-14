<?php

namespace App\Http\Controllers\Api\V1\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AdsBannerResource;
use App\Http\Resources\Api\BannerResource;
use App\Http\Resources\Api\BannerTextResource;
use App\Http\Resources\Api\ContactResource;
use App\Http\Resources\Api\GameProviderResource;
use App\Http\Resources\Api\GameTypeResource;
use App\Http\Resources\Api\PromotionResource;
use App\Models\Admin\Banner;
use App\Models\Admin\BannerAds;
use App\Models\Admin\BannerText;
use App\Models\Admin\GameType;
use App\Models\Admin\Promotion;
use App\Models\Admin\TopTenWithdraw;
use App\Models\Contact;
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

    public function gameTypes()
    {
        $types = GameType::active()->get();
        return $this->success(GameTypeResource::collection($types));
    }

    public function providers($id)
    {
        $providers = GameType::with(['products' => function ($query) {
            $query->where('status', 1);
            $query->orderBy('order', 'asc');
        }])->where('id', $id)->where('status', 1)->first();
        if($providers){
            return $this->success(new GameProviderResource($providers));
        }else{
            return $this->error('', 'Providers Not Found', 404);
        }
    }
}
