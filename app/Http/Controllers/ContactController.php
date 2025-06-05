<?php

namespace App\Http\Controllers;

use App\Mail\ContactMailManager;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Mail;

class ContactController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_all_contacts'])->only('index');
        $this->middleware(['permission:reply_to_contact'])->only('reply_modal');
    }

    public function index()
    {
        $contacts = Contact::orderBy('id', 'desc')->paginate(20);
        return view('backend.support.contact.contacts', compact('contacts'));
    }

    public function query_modal(Request $request)
    {
        $contact = Contact::findOrFail($request->id);
        return view('backend.support.contact.query_modal', compact('contact'));
    }

    public function reply_modal(Request $request)
    {
        $contact = Contact::findOrFail($request->id);
        return view('backend.support.contact.reply_modal', compact('contact'));
    }

    public function reply(Request $request)
    {
        $contact = Contact::findOrFail($request->contact_id);
        $admin = User::where('user_type', 'admin')->first();

        $array['name'] = $admin->name;
        $array['email'] = $admin->email;
        $array['phone'] = $admin->phone;
        $array['content'] = str_replace("\n", "<br>", $request->reply);
        $array['subject'] = translate('Query Contact Reply');
        $array['from'] = $admin->email;

        try {
            Mail::to($contact->email)->queue(new ContactMailManager($array));
            $contact->update([
                'reply' => $request->reply,
            ]);
        } catch (\Exception $e) {
            flash(translate('Something Went wrong'))->error();
            return back();
        }
        flash(translate('Reply has been sent successfully'))->success();
        return back();
    }

    public function contact(Request $request)
    {
        $admin = User::where('user_type', 'admin')->first();

        $array['name'] = $request->name;
        $array['email'] = $request->email;
        $array['phone'] = $request->phone;
        $array['content'] = str_replace("\n", "<br>", $request->content);
        $array['subject'] = translate('Query Contact');
        $array['from'] = $request->email;

        try {
            Mail::to($admin->email)->queue(new ContactMailManager($array));
            Contact::insert([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'content' => $request->content,
            ]);
        } catch (\Exception $e) {
            flash(translate('Something Went wrong'))->error();
            return back();
        }
        flash(translate('Query has been sent successfully'))->success();
        return back();
    }
}
