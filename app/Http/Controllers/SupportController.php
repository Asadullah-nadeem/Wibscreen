<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'category' => 'required',
            'name'     => 'required',
            'email'    => 'required|email',
            'message'  => 'required',
        ]);

        Mail::send([], [], function ($message) use ($data) {
            $message->to('support.codeaxe@gmail.com')
                ->subject('New Support Request: ' . $data['category'])
                ->html("
                    <h3>New Support Request</h3>
                    <p><strong>Name:</strong> {$data['name']}</p>
                    <p><strong>Email:</strong> {$data['email']}</p>
                    <p><strong>Category:</strong> {$data['category']}</p>
                    <p><strong>Message:</strong><br>{$data['message']}</p>
                ");
        });

        return back()->with('success', 'Message sent! We\'ll respond within 24 hours.');
    }
}
