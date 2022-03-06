<?php

namespace App\Jobs\Videos;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class getObjectTags implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The podcast instance.
     *
     * @var \App\Models\Video
     */
    protected $video;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->get_ml_tags();
    }

    public function get_ml_tags(){//$video_path = null
        /*
        if($video_path != null){
            $path = $video_path;
            $video_id = Video::where('path', $video_path)->first()->id;
        }else{
            $video_id = request()->vid_id;
            $path = Video::where('id', $video_id)->first()->path;
        }*/
        $video_id = $this->video->id;
        $path = $this->video->path;
        
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
