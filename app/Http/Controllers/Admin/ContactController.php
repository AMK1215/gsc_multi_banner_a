<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    protected const SUB_AGENT_ROlE = 'Sub Agent';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agent = $this->getAgent() ?? Auth::user();

        $contacts = Contact::where('agent_id', $agent->id)->get();

        return view('admin.contact.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.contact.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'value' => 'required',
        ]);
        $agent = $this->getAgent() ?? Auth::user();

        Contact::create([
            'name' => $request->name,
            'value' => $request->value,
            'agent_id' => $agent->id,
        ]);

        return redirect(route('admin.contact.index'))->with('success', 'New Contact Created Successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        return view('admin.contact.edit', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'name' => 'required',
            'value' => 'required',
        ]);

        $contact->update([
            'name' => $request->name,
            'value' => $request->value,
        ]);

        return redirect(route('admin.contact.index'))->with('success', 'New Contact Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect(route('admin.contact.index'))->with('success', 'New Contact Updated Successfully.');
    }

    private function isExistingAgent($userId)
    {
        $user = User::find($userId);

        return $user && $user->hasRole(self::SUB_AGENT_ROlE) ? $user->parent : null;
    }

    private function getAgent()
    {
        return $this->isExistingAgent(Auth::id());
    }
}
