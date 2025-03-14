<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    use HttpResponses;

    public function contact()
    {
        $player = Auth::user();
        $contact = Contact::where('agent_id', $player->agent_id)->get();

        return $this->success($contact, 'Contact List');
    }
}
