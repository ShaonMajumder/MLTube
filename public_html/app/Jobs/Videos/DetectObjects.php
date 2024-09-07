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
use Illuminate\Support\Facades\Log;

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
        $video_id = $this->video->id;
        Video::where('id', $video_id)
            ->update(['ml_tags' => $tags]);
    }

    public function get_ml_tags($video_path = null){

        if ($video_path !== null) {
            $video = Video::where('path', $video_path)->first();
        } else {
            $video_id = $this->video->id;
            $video = Video::find($video_id);
        }

        if (!$video) {
            throw new \Exception("Video not found");
        }
        
        $path = $video->path;
        $abs_path = Storage::path($path);
        $script_path = env('ML_SCRIPT_PATH');

        if (!file_exists($script_path)) {
            throw new \Exception("Script path not found");
        }

        $command = escapeshellcmd("python3 $script_path --input=\"$abs_path\" --input_type=video");
        $output = shell_exec($command);

        if ($output === null) {
            throw new \Exception("Error executing the script");
        }

        Log::info("ML tags: ".$output);
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