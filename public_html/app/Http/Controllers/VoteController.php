<?php

namespace App\Http\Controllers;

use App\Enums\ModelEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function vote(Request $request, $type){
        $entityId = $request->entity_id;
        $entityType = ModelEnum::MODELS[$request->entity_type];
        $entity = $this->getEntity($entityId,$entityType); 
        return auth()->user()->toggleVote($entity, $type);
    }

    public function getEntity($entityId, $entityType){
        $entity = null;
        // switch($entityType){
        //     case Video::class:
        //         $entity = Video::find($entityId);
        //         break;
        //     case Comment::class:
        //         $entity = Comment::find($entityId);
        //         break;
        //     default:
        //         throw new ModelNotFoundException('Entity Type not found.');
        // }
        $entity = $entityType::find($entityId);
        if($entity) {
            return $entity;
        } else {
            throw new ModelNotFoundException('Entity not found.');
        }
        
    }
}
