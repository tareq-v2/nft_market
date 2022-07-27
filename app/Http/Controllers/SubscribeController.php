<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SubscribeMail;
use Illuminate\Support\Facades\mail;
use App\Models\Subscribe;

class SubscribeController extends Controller
{
    public function user_subscribe(Request $request){
        $request->validate(['email' => 'required|unique:subscribes'],['email.unique' => "You are already subscribed !"]);
        $email = $request->email;
        $mail = Subscribe::create($request->except('_token'));

        if($mail){
            $data = "thanks for subscribe our app !";
            Mail::to($email)->send(new SubscribeMail($data));
            return back()->withSuccess('Subscribed !');
        }
    }
}
