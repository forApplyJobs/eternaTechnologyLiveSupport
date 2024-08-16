<?php

namespace App\Http\Controllers;

use App\Events\NewCall;
use Illuminate\Http\Request;
use App\Events\NewOffer;
use App\Events\NewAnswer;
use App\Events\NewIceCandidate;

class WebRTCController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Handle new offer and broadcast it.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendOffer(Request $request)
    {
        $offer = $request->validate([
            'offer' => 'required|array',
            'receiver_id' => 'required|integer',
        ]);
        // dd(new NewOffer($offer));
        broadcast(new NewOffer($offer));

        return response()->json(['status' => 'Offer sent']);
    }

    /**
     * Handle new answer and broadcast it.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendAnswer(Request $request)
    {
        $answer = $request->validate([
            'answer' => 'required|array',
            'receiver_id' => 'required|integer',
        ]);


        broadcast(new NewAnswer($answer));

        return response()->json(['status' => 'Answer sent']);
    }

    /**
     * Handle new ICE candidate and broadcast it.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendIceCandidate(Request $request)
    {
        $iceCandidate = $request->validate([
            'iceCandidate' => 'required|array',
            'receiver_id' => 'required|integer',
        ]);
        broadcast(new NewIceCandidate($iceCandidate));

        return response()->json(['status' => 'ICE candidate sent']);
    }
    public function sendCallRequest(Request $request)
    {
        $call = $request->validate([
            'call_from' => 'required|integer',
            'receiver_id' => 'required|integer',
            'status'=>'required|string'
        ]);
        // dd($call);
        broadcast(new NewCall($call));

        return response()->json(['status' => 'Call request sent']);
    }
}
