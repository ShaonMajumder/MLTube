<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class ChannelSubscriptionController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Channel $channel)
    {
        
        return $channel->subscriptions()->create([
            'user_id' => auth()->user()->id
        ]);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Channel $channel, Subscription $subscription)
    {
        $subscription->delete();
        return response()->json([]);
    }

    public function user(User $user){
        return view('user', compact('user'));
    }

    public function listSubscriptions(User $user){
        $subscriptions = $user->subscriptions()->paginate(10);
        return view('channels.subscriptions', compact('subscriptions'));
    }

    public function listChannelSubscribers(Channel $channel){
        if( $channel->id != auth()->user()->channel->id ){
            abort(403);
        }
        $subscribers = $channel->subscribers()->paginate(10);
        return view('channels.subscribers', compact('subscribers'));
    }
}
