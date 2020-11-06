<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function initialise(Request $request){
        $users = User::all();

        return view('auth.chat')->with('users', $users);
    }

    public function getMessage(Request $request){
        $user_curr = $request->get('user_curr');
        $destinataire = $request->get('destinataire');
        $destinataire = User::where('name', $destinataire)->first();

        $messages = Message::where('from', $user_curr)->where('destinataire', $destinataire->id)->get();
        $messages_other = Message::where('from', $destinataire->id)->where('destinataire', $user_curr)->get();
        
        return [$messages, $messages_other];
    }

    public function sendMessage(Request $request){

        $user = $request->get('userId');
        $content = $request->get('content');
        $name = $request->input('destinataire');
        $destinataire = User::where('name', $name)->first();

        $message = new Message;
        $message->content = $content;
        $message->destinataire = $destinataire->id;
        $message->from = $user;

        $message->save();

        return 'Message Envoyé';
    }

    public function updateMessage(Request $request){
        
        $user_curr = $request->get('user_curr');
        $destinataire = $request->get('destinataire');
        $destinataire = User::where('name', $destinataire)->first();
        $oldMessages = $request->get('allMessage');

        $messages = Message::where('from', $user_curr)->where('destinataire', $destinataire->id)->get();
        $messages_other = Message::where('from', $destinataire->id)->where('destinataire', $user_curr)->get();
        return [$messages, $messages_other];
    }
}
