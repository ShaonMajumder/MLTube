<?php

namespace App\Jobs\Videos;

use Illuminate\Support\Facades\Storage;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetectObjects implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {       
        $tags = $this->get_ml_tags();
        $this->save_tags($tags);
    }

    public function get_ml_tags($video_path = null){
        if($video_path != null){
            $path = $video_path;
            $video_id = Video::where('path', $video_path)->first()->id;
        }else{
            $video_id = $this->video->id;//request()->vid_id;
            $path = Video::where('id', $video_id)->first()->path;
        }
        
        $abs_path = Storage::path($path) ;
        //return asset( Storage::url($path) );
        //https://stackoverflow.com/questions/41020068/running-python-script-in-laravel
        // abs file path - https://laravel.com/docs/8.x/filesystem
        $script_name = "main.py";
        // $ml_path = "D:/Projects/object_detection";
        $ml_path = "/home/shaon/Projects/Object-Detection-YoloV4";
        
        
        $output = shell_exec("python3 $ml_path/$script_name --input=\"$abs_path\" --input_type=video");
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
 
    public function save_tags($tags){
        $video_id = $this->video->id;
        Video::where('id', $video_id)->update(['ml_tags' => $tags]);
    }
    
}