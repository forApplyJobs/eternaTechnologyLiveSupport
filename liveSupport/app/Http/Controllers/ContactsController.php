<?php

namespace App\Http\Controllers;
use App\Events\NewMessage;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Message;
class ContactsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function get(){
        $contacts = User::where('id', '!=', auth()->id())->get();
        return response()->json($contacts);
    }
    public function getMessagesFor($id){
        $messages= Message::where('messageFrom',$id)->orWhere('messageTo',$id)->get();
        return response()->json($messages);
    }
    public function send(Request $request){
        $message = Message::create([
            'messageFrom'=>auth()->id(),
            'messageTo'=>$request->contact_id,
            'text'=>$request->text
        ]);

        broadcast(new NewMessage($message))->toOthers();
        return response()->json($message);
    }
}
