<?php

namespace App\Http\Controllers;

use FFMpeg; //use Pbmedia/LaravelFFMpeg/FFMpeg;
use App\Jobs\Videos\ConvertForStreaming;
use App\Jobs\Videos\CreateVideoThumbnail;
use App\Jobs\Videos\DetectObjects;
use App\Models\Channel;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
//insgrowthsalary

class UploadVideoController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(Channel $channel){
        return view('channels.upload', [
            'channel' => $channel
        ]);
    }

    public function store(Channel $channel){
        $video = $channel->videos()->create([
            'title' => request()->title,
            'path' => request()->video->store("channels/{$channel->id}")
        ]);
        
        $this->dispatch(new CreateVideoThumbnail($video));
        $this->dispatch(new ConvertForStreaming($video));
        //$this->dispatch(new DetectObjects($video));
        return $video;
    }

    //Video $video
    public function get_ml_tags($video_path = null){
        if($video_path != null){
            $path = $video_path;
            $video_id = Video::where('path', $video_path)->first()->id;
        }else{
            $video_id = request()->vid_id;
            $path = Video::where('id', $video_id)->first()->path;
        }
        
        $abs_path = Storage::path($path) ;
        //return asset( Storage::url($path) );
        //https://stackoverflow.com/questions/41020068/running-python-script-in-laravel
        // abs file path - https://laravel.com/docs/8.x/filesystem
        $script_name = "API_split10_video_dic.py";
        $ml_path = "D:/Projects/object_detection";
        $output = shell_exec("python $ml_path/$script_name --in_video=\"$abs_path\"");
        Video::where('id', $video_id)->update(['ml_tags' => $output]);
        return $output;

        
        
        /*
        $process = new Process(["python", "D:/Projects/object_detection/$script_name","--in_video=\"$abs_path\""]);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
        */

    }
}
