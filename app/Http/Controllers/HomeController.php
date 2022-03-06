<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\Video;
//use App\Models\History;
use App\Models\Search;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    
    public function index()
    {
        return view('home');
    }

    public function search()
    {
        $query = request()->q;
        
        $videos = collect();
        $channels = collect();

        if ($query) {
            $search = new Search;
            if(auth()->check() ){
                $search->user_id = auth()->user()->id;
            }else{
                $search->user_id = ''; //guest_session_id
            }
            $search->page_id = '';
            $search->query = $query;
            $search->save();

            $videos = Video::where('title', 'LIKE', "%{$query}%")
                            ->orWhere('description', 'LIKE', "%{$query}%")
                            ->orWhere('ml_tags', 'LIKE', "%{$query}%")
                            ->paginate(5, ['*'], 'video_page');
                            
            $channels = Channel::where('name', 'LIKE', "%{$query}%")->orWhere('description', 'LIKE', "%{$query}%")->paginate(5, ['*'], 'channel_page');
            return view('search')->with([
                'videos' => $videos,
                'channels' => $channels
            ]);
        }else{
            return redirect('home');
        }

        
    }
}
