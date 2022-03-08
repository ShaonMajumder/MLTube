<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    protected $fields = ["id", "channel_id", "views", "thumbnail", "percentage", "title", "description", "ml_tags", "path", "created_at", "updated_at"];
    protected $without_fields = []; 
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    /**
     * Hide Properties From Resoonse
     */
    public function hide($hide_fields){
        if( !is_array($hide_fields) ){
            $hide_fields = (array)$hide_fields;
        }
        $this->without_fields = $hide_fields;
        return $this;
    }

    public function accept($accept_fields){
        if( !is_array($accept_fields) ){
            $accept_fields = (array)$accept_fields;
        }
        
        $this->without_fields = array_diff( $this->fields,$accept_fields);
        return $this;
    }

    /**
     * Filter the response And Hide Unwanted Properties
     */
    protected function filterArray($result = []){
        return collect($result)->forget($this->without_fields);
    }

    /**
     * Return Response Into Array
     */
    public function toArray($request)
    {
        return $this->filterArray([ 
            "id" => $this->id,
            "channel_id" => $this->channel_id,
            "views" => $this->views,
            "thumbnail" => $this->thumbnail,
            "percentage" => $this->percentage,
            "title" => $this->title,
            "description" => $this->description,
            "ml_tags" => $this->ml_tags,
            "path" => $this->path,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ]);
    }
}
